<?php

namespace App\Livewire\Payment;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Payment;
use App\Services\Payment\Factories\PaymentGatewayFactory;
use Illuminate\Support\Facades\Auth;
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

    public function mount()
    {
        $user = Auth::user();

        // Set default payment type based on user's membership status
        $this->paymentType = $user->getPaymentType();

        // Calculate amount based on user's membership status
        $this->calculatedAmount = $user->calculatePaymentAmount();
        $this->amount = $this->calculatedAmount;

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

            // Process payment through gateway if needed
            if ($this->paymentMethod === PaymentMethod::ChipInAsia->value) {
                $gatewayFactory = new PaymentGatewayFactory;
                $gateway = $gatewayFactory->make('chipin');

                $result = $gateway->processPayment([
                    'amount' => $this->amount,
                    'reference' => $payment->reference_id,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                ]);

                if ($result['success']) {
                    $payment->update([
                        'reference_id' => $result['transaction_id'],
                        'status' => PaymentStatus::PROCESSING,
                    ]);

                    $this->showProcessingModal = false;

                    // Redirect to payment gateway
                    return redirect()->to($result['redirect_url']);
                } else {
                    throw new \Exception($result['error'] ?? 'Failed to process payment');
                }
            } else {
                // For non-gateway payments, update the payment status
                $payment->update([
                    'status' => PaymentStatus::PENDING->value,
                ]);

                $this->showProcessingModal = false;

                return redirect()->route('payments.show', $payment)->with('success', 'Pembayaran telah direkodkan. Sila tunggu untuk pengesahan.');
            }
        } catch (\Exception $e) {
            $this->showProcessingModal = false;
            $this->addError('payment', 'Failed to process payment: '.$e->getMessage());
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
            'paymentMethods' => PaymentMethod::toArray(),
        ]);
    }
}
