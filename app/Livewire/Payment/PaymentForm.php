<?php

namespace App\Http\Livewire\Payment;

use App\Actions\Payments\CreatePaymentAction;
use App\Actions\Payments\ProcessPaymentAction;
use App\Actions\Payments\UploadReceiptAction;
use App\Actions\Payments\ValidatePaymentAction;
use App\Enums\PaymentMethod;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class PaymentForm extends Component
{
    use WithFileUploads;

    public $amount = 20.00;

    public $paymentMethod = 'bank_transfer';

    public $month;

    public $year;

    public $householdCount = 1;

    public $notes;

    public $receiptImage;

    // For online payment
    public $paymentUrl;

    public $showPaymentRedirect = false;

    // For bank transfer
    public $bankDetails;

    public $showBankDetails = false;

    protected $listeners = [
        'refreshPaymentForm' => '$refresh',
    ];

    /**
     * Component mount method.
     */
    public function mount()
    {
        // Set default month and year
        $this->month = now()->format('F');
        $this->year = now()->year;
    }

    /**
     * Calculate the amount based on household count.
     */
    public function updatedHouseholdCount()
    {
        $this->amount = $this->householdCount * 20.00;
    }

    /**
     * Process the payment form submission.
     */
    public function submitPayment(
        CreatePaymentAction $createPaymentAction,
        ProcessPaymentAction $processPaymentAction,
        UploadReceiptAction $uploadReceiptAction,
        ValidatePaymentAction $validatePaymentAction
    ) {
        try {
            // Validate form data
            $validated = $validatePaymentAction->execute([
                'user_id' => Auth::id(),
                'amount' => $this->amount,
                'payment_method' => $this->paymentMethod,
                'month' => $this->month,
                'year' => $this->year,
                'household_count' => $this->householdCount,
                'notes' => $this->notes,
                'receipt_image' => $this->receiptImage,
            ]);

            // Create payment record
            $payment = $createPaymentAction->execute([
                'user_id' => Auth::id(),
                'amount' => $this->amount,
                'payment_method' => $this->paymentMethod,
                'month' => $this->month,
                'year' => $this->year,
                'household_count' => $this->householdCount,
                'notes' => $this->notes,
            ]);

            if (!$payment) {
                $this->dispatch('show-error', [
                    'message' => 'Failed to create payment record.',
                ]);

                return;
            }

            // If payment method is bank transfer, handle receipt upload
            if ($this->paymentMethod === PaymentMethod::BankTransfer->value && $this->receiptImage) {
                $uploadResult = $uploadReceiptAction->execute($payment, $this->receiptImage);

                if (!$uploadResult['success']) {
                    $this->dispatch('show-error', [
                        'message' => $uploadResult['message'],
                    ]);

                    return;
                }
            }

            // Process payment based on method
            $processResult = $processPaymentAction->execute($payment, [
                'customer_name' => Auth::user()->name,
                'customer_email' => Auth::user()->email,
                'redirect_url' => route('payment.callback', ['payment_id' => $payment->id]),
                'webhook_url' => route('payment.webhook'),
            ]);

            if (!$processResult['success']) {
                $this->dispatch('show-error', [
                    'message' => $processResult['message'],
                ]);

                return;
            }

            // Handle payment method specific logic
            if ($this->paymentMethod === PaymentMethod::ChipInAsia->value) {
                // For online payment, redirect to payment URL
                $this->paymentUrl = $processResult['payment_url'];
                $this->showPaymentRedirect = true;
            } elseif ($this->paymentMethod === PaymentMethod::BankTransfer->value) {
                // For bank transfer, show bank details
                $this->bankDetails = $processResult['bank_details'];
                $this->showBankDetails = true;
            }

            // Reset form
            $this->reset(['receiptImage', 'notes']);

            $this->dispatch('show-success', [
                'message' => 'Payment submitted successfully.',
            ]);

            // Emit event to refresh payment list
            $this->emit('paymentCreated');

        } catch (\Exception $e) {
            $this->dispatch('show-error', [
                'message' => 'Error: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Get available payment methods.
     */
    public function getPaymentMethodsProperty()
    {
        return PaymentMethod::toArray();
    }

    /**
     * Close modals.
     */
    public function closeModals()
    {
        $this->showPaymentRedirect = false;
        $this->showBankDetails = false;
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.payment.payment-form');
    }
}
