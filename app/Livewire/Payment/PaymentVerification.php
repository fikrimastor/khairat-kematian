<?php

namespace App\Http\Livewire\Payment;

use App\Actions\Payments\VerifyPaymentAction;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PaymentVerification extends Component
{
    public $paymentId;

    public $status = 'verified';

    public $notes;

    public $payment;

    public $verificationResult = null;

    /**
     * Component mount method.
     *
     * @param  mixed  $paymentId
     */
    public function mount($paymentId)
    {
        $this->paymentId = $paymentId;
        $this->payment = Payment::with(['user', 'receipt'])->findOrFail($paymentId);
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
            $this->paymentId,
            $this->status,
            $this->notes
        );

        $this->verificationResult = $result;

        if ($result['success']) {
            // Refresh payment data
            $this->payment = Payment::with(['user', 'receipt'])->find($this->paymentId);

            // Clear form
            $this->reset(['notes']);

            // Show success message
            $this->dispatch('show-success', [
                'message' => $result['message'],
            ]);

            // Emit event to refresh payment status
            $this->dispatch('refreshPaymentStatus');
        } else {
            // Show error message
            $this->dispatch('show-error', [
                'message' => $result['message'],
            ]);
        }
    }

    /**
     * Get available status options.
     */
    public function getStatusOptionsProperty()
    {
        return [
            'verified' => 'Verify Payment',
            'rejected' => 'Reject Payment',
        ];
    }

    /**
     * Check if the payment can be verified.
     */
    public function getCanBeVerifiedProperty()
    {
        return in_array($this->payment->status, [
            PaymentStatus::PENDING,
            PaymentStatus::PROCESSING,
        ]);
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.payment.payment-verification');
    }
}
