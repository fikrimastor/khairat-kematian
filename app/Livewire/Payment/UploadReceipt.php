<?php

namespace App\Livewire\Payment;

use App\Actions\Payment\UploadReceiptAction;
use App\Models\Payment;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadReceipt extends Component
{
    use WithFileUploads;

    public Payment $payment;

    public $receiptFile;

    public $receiptNotes;

    public $showUploadModal = false;

    public function mount(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function submitReceipt()
    {
        $this->validate([
            'receiptFile' => 'required|file|max:2048|mimes:jpg,jpeg,png,pdf',
            'receiptNotes' => 'nullable|string|max:255',
        ]);

        try {
            app(UploadReceiptAction::class)->execute(
                $this->payment,
                $this->receiptFile,
                $this->receiptNotes
            );

            $this->showUploadModal = false;
            $this->reset(['receiptFile', 'receiptNotes']);

            // Refresh the payment model
            $this->payment = Payment::find($this->payment->id);

            $this->dispatch('receipt-uploaded');
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('Receipt uploaded successfully. Your payment is pending verification.'),
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => __('Failed to upload receipt: ').$e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.payment.upload-receipt');
    }
}
