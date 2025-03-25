<?php

namespace App\Livewire\Payment;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Payment;
use App\Services\Payment\Factories\PaymentGatewayFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class PaymentForm extends Component
{
    public $paymentType = '';

    public $paymentMethod = '';

    public $amount = 0;

    public $reference = '';

    public $calculatedAmount = 0;

    public $isAmountReadOnly = true;

    public $showSummary = false;

    public $showProcessingModal = false;

    public $errorMessage = '';

    public $availablePaymentMethods = [];

    public function mount()
    {
        $user = Auth::user();

        // Set default payment type based on user's membership status
        $this->paymentType = $user->getPaymentType();

        // Calculate amount based on user's membership status
        $this->calculatedAmount = $user->calculatePaymentAmount();
        $this->amount = $this->calculatedAmount;

        // Get available payment methods
        $this->availablePaymentMethods = PaymentMethod::toArray();

        // Show summary
        $this->updateSummary();
    }

    public function updatedPaymentType()
    {
        $user = Auth::user();

        // Set amount based on payment type
        if ($this->paymentType === PaymentType::REGISTRATION->value) {
            $this->calculatedAmount = config('khairat.registration_fee', 50);
        } elseif ($this->paymentType === PaymentType::RENEWAL->value) {
            $this->calculatedAmount = config('khairat.renewal_fee', 40);
        } else {
            $this->calculatedAmount = 0;
        }

        $this->amount = $this->calculatedAmount;
        $this->updateSummary();
    }

    public function updatedPaymentMethod()
    {
        $this->updateSummary();
    }

    public function updatedAmount()
    {
        $this->updateSummary();
    }

    private function updateSummary()
    {
        $this->showSummary = !empty($this->paymentType) && !empty($this->paymentMethod) && $this->amount > 0;
    }

    public function save()
    {
        $this->validate([
            'paymentType' => 'required|string|in:'.implode(',', array_column(PaymentType::cases(), 'value')),
            'paymentMethod' => 'required|string|in:'.implode(',', array_column(PaymentMethod::cases(), 'value')),
            'amount' => 'required|numeric|min:1',
            'reference' => 'nullable|string|max:255',
        ]);

        $this->showProcessingModal = true;
        $this->errorMessage = '';

        try {
            $user = Auth::user();
            $dependentCount = $user->dependents()->count();

            // Create payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'amount' => $this->amount,
                'payment_method' => $this->paymentMethod,
                'payment_type' => $this->paymentType,
                'reference_id' => 'PAY-'.time().'-'.$user->id,
                'status' => PaymentStatus::PENDING,
                'month' => date('F'),
                'year' => date('Y'),
                'household_count' => $dependentCount + 1, // User + dependents
                'notes' => $this->reference,
            ]);

            Log::info('Payment record created', [
                'payment_id' => $payment->id,
                'user_id' => $user->id,
                'amount' => $this->amount,
                'payment_method' => $this->paymentMethod,
            ]);

            // Process payment through gateway if needed
            if (in_array($this->paymentMethod, [PaymentMethod::CHIP_IN_ASIA->value, PaymentMethod::Billplz->value])) {
                return $this->processOnlinePayment($payment, $user);
            } else {
                return $this->processManualPayment($payment);
            }
        } catch (\Exception $e) {
            Log::error('Payment processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payment_data' => [
                    'user_id' => Auth::id(),
                    'amount' => $this->amount,
                    'payment_method' => $this->paymentMethod,
                    'payment_type' => $this->paymentType,
                ],
            ]);

            $this->showProcessingModal = false;
            $this->errorMessage = 'Ralat berlaku semasa memproses pembayaran: '.$e->getMessage();
        }
    }

    /**
     * Process online payment through payment gateway
     *
     * @param  mixed  $payment
     * @param  mixed  $user
     */
    private function processOnlinePayment($payment, $user)
    {
        try {
            $gatewayFactory = new PaymentGatewayFactory;

            // Determine which gateway to use based on payment method
            $paymentMethodEnum = match ($this->paymentMethod) {
                PaymentMethod::CHIP_IN_ASIA->value => PaymentMethod::CHIP_IN_ASIA,
                PaymentMethod::Billplz->value => PaymentMethod::Billplz,
                default => throw new \Exception('Unsupported online payment method')
            };

            $gateway = $gatewayFactory->make($paymentMethodEnum);

            $result = $gateway->processPayment([
                'amount' => $this->amount,
                'reference' => $payment->reference_id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'payment_type' => $this->paymentType,
            ]);

            if ($result['success']) {
                $payment->update([
                    'reference_id' => $result['transaction_id'],
                    'status' => PaymentStatus::PROCESSING,
                ]);

                Log::info('Payment processed successfully', [
                    'payment_id' => $payment->id,
                    'transaction_id' => $result['transaction_id'],
                    'status' => PaymentStatus::PROCESSING,
                    'gateway' => $this->paymentMethod,
                ]);

                $this->showProcessingModal = false;

                // Redirect to payment gateway
                return redirect()->to($result['redirect_url']);
            } else {
                throw new \Exception($result['error'] ?? 'Gagal memproses pembayaran');
            }
        } catch (\Exception $e) {
            Log::error('Online payment processing error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Update payment status to failed
            $payment->update([
                'status' => PaymentStatus::FAILED,
            ]);

            $this->showProcessingModal = false;
            $this->errorMessage = 'Ralat berlaku semasa memproses pembayaran: '.$e->getMessage();
        }
    }

    /**
     * Process manual payment (bank transfer)
     *
     * @param  mixed  $payment
     */
    private function processManualPayment($payment)
    {
        try {
            // For bank transfers, get the payment details
            if ($this->paymentMethod === PaymentMethod::BANK_TRANSFER->value) {
                $gatewayFactory = new PaymentGatewayFactory;

                // Convert string to PaymentMethod enum
                $methodEnum = PaymentMethod::from($this->paymentMethod);
                $gateway = $gatewayFactory->make($methodEnum);

                $result = $gateway->getPaymentUrl([
                    'amount' => $this->amount,
                    'reference' => $payment->reference_id,
                ]);

                // Store bank details in session for display
                session()->flash('bank_details', $result['bank_details'] ?? null);
                session()->flash('payment_instructions', $result['instructions'] ?? null);
            }

            // Update payment status
            $payment->update([
                'status' => PaymentStatus::PENDING->value,
            ]);

            Log::info('Manual payment recorded', [
                'payment_id' => $payment->id,
                'payment_method' => $this->paymentMethod,
            ]);

            $this->showProcessingModal = false;

            return redirect()->route('payments.show', $payment)
                ->with('success', 'Pembayaran telah direkodkan. Sila tunggu untuk pengesahan.');
        } catch (\Exception $e) {
            Log::error('Manual payment processing error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->showProcessingModal = false;
            $this->errorMessage = 'Ralat berlaku semasa memproses pembayaran: '.$e->getMessage();
        }
    }

    public function cancel()
    {
        return redirect()->route('payments.index');
    }

    public function render()
    {
        return view('livewire.payment.payment-form', [
            'paymentTypes' => PaymentType::toArray(),
            'paymentMethods' => $this->availablePaymentMethods,
        ]);
    }
}
