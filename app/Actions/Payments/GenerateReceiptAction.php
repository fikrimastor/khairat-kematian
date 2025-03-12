<?php

namespace App\Actions\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateReceiptAction
{
    /**
     * Generate a receipt for a verified payment.
     *
     * @param  Payment  $payment  The payment to generate a receipt for
     * @return Receipt|null The generated receipt or null on failure
     */
    public function execute(Payment $payment): ?Receipt
    {
        try {
            // Check if payment is verified
            if ($payment->status !== PaymentStatus::VERIFIED) {
                Log::warning('Cannot generate receipt for unverified payment', [
                    'payment_id' => $payment->id,
                    'status' => $payment->status->value,
                ]);

                return null;
            }

            // Check if receipt already exists
            if ($payment->receipt) {
                Log::info('Receipt already exists for payment', [
                    'payment_id' => $payment->id,
                    'receipt_id' => $payment->receipt->id,
                ]);

                return $payment->receipt;
            }

            // Generate receipt
            $receiptNumber = Receipt::generateReceiptNumber();

            $receipt = Receipt::create([
                'payment_id' => $payment->id,
                'receipt_number' => $receiptNumber,
                'generated_at' => now(),
            ]);

            Log::info('Receipt generated successfully', [
                'payment_id' => $payment->id,
                'receipt_id' => $receipt->id,
                'receipt_number' => $receipt->receipt_number,
            ]);

            return $receipt;
        } catch (\Exception $e) {
            Log::error('Failed to generate receipt', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Generate a PDF receipt file and save it to storage.
     *
     * @param  Receipt  $receipt  The receipt to generate a PDF for
     * @return string|null The path to the generated PDF or null on failure
     */
    public function generatePdf(Receipt $receipt): ?string
    {
        try {
            // Get payment details
            $payment = $receipt->payment;

            // Generate PDF filename
            $filename = 'receipts/'.$receipt->receipt_number.'.pdf';

            // This is a placeholder for actual PDF generation
            // In a real implementation, you would use a PDF library like DomPDF, TCPDF, etc.
            $pdfContent = "Receipt for Payment #{$payment->id}\n";
            $pdfContent .= "Receipt #: {$receipt->receipt_number}\n";
            $pdfContent .= "Date: {$receipt->generated_at->format('d-m-Y')}\n";
            $pdfContent .= "Amount: RM{$payment->amount}\n";
            $pdfContent .= "Payment Method: {$payment->payment_method->value}\n";
            $pdfContent .= "Month: {$payment->month} {$payment->year}\n";

            // Save PDF to storage
            Storage::put($filename, $pdfContent);

            // Update receipt with file path
            $receipt->update([
                'receipt_path' => $filename,
            ]);

            Log::info('Receipt PDF generated', [
                'receipt_id' => $receipt->id,
                'path' => $filename,
            ]);

            return $filename;
        } catch (\Exception $e) {
            Log::error('Failed to generate receipt PDF', [
                'receipt_id' => $receipt->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
