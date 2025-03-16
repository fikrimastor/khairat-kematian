<?php

namespace App\Livewire\Payment;

use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentList extends Component
{
    use WithPagination;

    public $showUploadModal = false;

    public $selectedPaymentId;

    public $receiptFile;

    public $receiptNotes;

    protected $listeners = [
        'payment-filter-applied' => '$refresh',
        'receipt-uploaded' => '$refresh',
    ];

    public function uploadReceipt($paymentId)
    {
        $this->selectedPaymentId = $paymentId;
        $this->showUploadModal = true;
    }

    public function closeUploadModal()
    {
        $this->showUploadModal = false;
        $this->reset(['selectedPaymentId', 'receiptFile', 'receiptNotes']);
    }

    public function submitReceipt()
    {
        $this->validate([
            'receiptFile' => 'required|file|max:2048|mimes:jpg,jpeg,png,pdf',
            'receiptNotes' => 'nullable|string|max:255',
        ]);

        $payment = Payment::findOrFail($this->selectedPaymentId);

        // Ensure the user can only upload receipts for their own payments
        if ($payment->user_id !== Auth::id()) {
            $this->addError('receiptFile', 'You are not authorized to upload a receipt for this payment.');

            return;
        }

        try {
            app(\App\Actions\Payment\UploadReceiptAction::class)->execute(
                $payment,
                $this->receiptFile,
                $this->receiptNotes
            );

            $this->showUploadModal = false;
            $this->reset(['selectedPaymentId', 'receiptFile', 'receiptNotes']);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('Receipt uploaded successfully. Your payment is pending verification.'),
            ]);
        } catch (\Exception $e) {
            $this->addError('receiptFile', 'Failed to upload receipt: '.$e->getMessage());
        }
    }

    public function render()
    {
        $query = Payment::where('user_id', Auth::id());

        // Apply filters from session if they exist
        if (session()->has('payment_filter.status') && session('payment_filter.status') !== '') {
            $query->where('status', session('payment_filter.status'));
        }

        if (session()->has('payment_filter.type') && session('payment_filter.type') !== '') {
            $query->where('payment_type', session('payment_filter.type'));
        }

        if (session()->has('payment_filter.dateFrom') && session('payment_filter.dateFrom') !== '') {
            $query->whereDate('created_at', '>=', session('payment_filter.dateFrom'));
        }

        if (session()->has('payment_filter.dateTo') && session('payment_filter.dateTo') !== '') {
            $query->whereDate('created_at', '<=', session('payment_filter.dateTo'));
        }

        $payments = $query->latest()->paginate(10);

        return view('livewire.payment.payment-list', [
            'payments' => $payments,
        ]);
    }
}
