<?php

namespace App\Http\Livewire\Payment;

use App\Actions\Payments\VerifyPaymentAction;
use App\Enums\PaymentStatus as PaymentStatusEnum;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PaymentStatus extends Component
{
    public Payment $payment;

    public $status;

    public $notes;

    public $showVerificationModal = false;

    public $verificationResult = null;

    protected $listeners = [
        'refreshPaymentStatus' => '$refresh',
    ];

    /**
     * Component mount method.
     */
    public function mount(Payment $payment)
    {
        $this->payment = $payment;
        $this->status = $payment->status->value;
    }

    /**
     * Show verification modal.
     */
    public function showVerification()
    {
        // Check if user can verify payments
        if (!Auth::user()->hasPermissionTo('payment.verify')) {
            $this->dispatch('show-error', [
                'message' => 'You do not have permission to verify payments.',
            ]);

            return;
        }

        $this->showVerificationModal = true;
    }

    /**
     * Process the payment verification.
     */
    public function verifyPayment(VerifyPaymentAction $verifyPaymentAction)
    {
        // Check if user can verify payments
        if (!Auth::user()->hasPermissionTo('payment.verify')) {
            $this->dispatch('show-error', [
                'message' => 'You do not have permission to verify payments.',
            ]);

            return;
        }

        // Validate inputs
        $this->validate([
            'status' => 'required|in:verified,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Process verification
        $result = $verifyPaymentAction->execute(
            $this->payment->id,
            $this->status,
            $this->notes
        );

        $this->verificationResult = $result;

        if ($result['success']) {
            // Refresh payment data
            $this->payment = Payment::find($this->payment->id);

            // Close modal
            $this->showVerificationModal = false;

            // Clear form
            $this->reset(['notes']);

            // Show success message
            $this->dispatch('show-success', [
                'message' => $result['message'],
            ]);

            // dispatch event to refresh payment list
            $this->dispatch('paymentStatusUpdated');
        } else {
            // Show error message
            $this->dispatch('show-error', [
                'message' => $result['message'],
            ]);
        }
    }

    /**
     * Cancel verification.
     */
    public function cancelVerification()
    {
        $this->showVerificationModal = false;
        $this->reset(['notes', 'verificationResult']);
        $this->status = $this->payment->status->value;
    }

    /**
     * Check if the payment can be verified.
     */
    public function getCanBeVerifiedProperty()
    {
        return in_array($this->payment->status, [
            PaymentStatusEnum::PENDING,
            PaymentStatusEnum::PROCESSING,
        ]);
    }

    /**
     * Check if the payment receipt is available.
     */
    public function getHasReceiptProperty()
    {
        return $this->payment->receipt && $this->payment->receipt->receipt_path;
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.payment.payment-status');
    }
}
