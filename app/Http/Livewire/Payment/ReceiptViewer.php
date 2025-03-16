<?php

namespace App\Http\Livewire\Payment;

use App\Models\Receipt;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ReceiptViewer extends Component
{
    use AuthorizesRequests;

    public Receipt $receipt;

    public bool $showDetails = false;

    /**
     * Mount the component.
     */
    public function mount(Receipt $receipt)
    {
        $this->receipt = $receipt;
        $this->authorize('view', $receipt);
    }

    /**
     * Toggle the details visibility.
     */
    public function toggleDetails()
    {
        $this->showDetails = !$this->showDetails;
    }

    /**
     * Get the download URL for the receipt.
     */
    public function getDownloadUrlProperty()
    {
        return route('receipts.download', $this->receipt);
    }

    /**
     * Get the payment associated with the receipt.
     */
    public function getPaymentProperty()
    {
        return $this->receipt->payment;
    }

    /**
     * Get the user associated with the receipt.
     */
    public function getUserProperty()
    {
        return $this->receipt->payment->user;
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.payment.receipt-viewer');
    }
}
