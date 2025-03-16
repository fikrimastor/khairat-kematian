<?php

namespace App\Actions\Payments;

use App\DTO\Payment\ReceiptData;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateReceiptAction
{
    /**
     * Generate a receipt for a payment.
     *
     * @param  int  $paymentId  The ID of the payment
     * @return Receipt The generated receipt
     */
    public function execute(int $paymentId): Receipt
    {
        // Get the payment
        $payment = Payment::with(['user', 'user.dependents'])->findOrFail($paymentId);

        // Check if payment is verified
        if ($payment->status !== PaymentStatus::VERIFIED->value) {
            throw new \InvalidArgumentException('Cannot generate receipt for unverified payment');
        }

        // Check if receipt already exists
        if ($payment->receipt) {
            return $payment->receipt;
        }

        // Generate receipt number
        $receiptNumber = Receipt::generateReceiptNumber();

        // Create receipt data
        $receiptData = new ReceiptData(
            paymentId: $payment->id,
            receiptNumber: $receiptNumber,
            generatedAt: now()
        );

        // Create receipt record
        $receipt = Receipt::create($receiptData->toArray());

        // Generate PDF
        $pdf = $this->generatePdf($receipt);

        // Store PDF
        $path = $this->storePdf($receipt, $pdf);

        // Update receipt with path
        $receipt->update(['receipt_path' => $path]);

        return $receipt;
    }

    /**
     * Generate a PDF for the receipt.
     *
     * @param  Receipt  $receipt  The receipt to generate a PDF for
     * @return mixed The generated PDF
     */
    private function generatePdf(Receipt $receipt)
    {
        $payment = $receipt->payment;
        $user = $payment->user;

        return PDF::loadView('receipts.pdf', [
            'receipt' => $receipt,
            'payment' => $payment,
            'user' => $user,
        ]);
    }

    /**
     * Store the PDF for the receipt.
     *
     * @param  Receipt  $receipt  The receipt to store the PDF for
     * @param  mixed  $pdf  The PDF to store
     * @return string The path to the stored PDF
     */
    private function storePdf(Receipt $receipt, $pdf): string
    {
        $filename = 'receipt_'.$receipt->receipt_number.'_'.Str::random(8).'.pdf';
        $path = 'receipts/'.$filename;

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
}
