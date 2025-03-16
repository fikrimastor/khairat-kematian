<?php

namespace App\Actions\Payment;

use App\Models\Payment;
use App\Models\PaymentProof;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UploadReceiptAction
{
    /**
     * Upload a receipt for a payment
     *
     * @param  Payment  $payment  The payment to upload a receipt for
     * @param  UploadedFile  $file  The receipt file
     * @param  string|null  $notes  Optional notes about the receipt
     * @return PaymentProof The created payment proof
     */
    public function execute(Payment $payment, UploadedFile $file, ?string $notes = null): PaymentProof
    {
        return DB::transaction(function () use ($payment, $file, $notes) {
            // Check if payment already has a proof
            if ($payment->paymentProof) {
                // Delete the old file if it exists
                if (Storage::exists($payment->paymentProof->file_path)) {
                    Storage::delete($payment->paymentProof->file_path);
                }

                // Update the existing proof
                $payment->paymentProof->update([
                    'file_path' => $this->storeFile($file, $payment),
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'notes' => $notes,
                ]);

                return $payment->paymentProof;
            }

            // Create a new payment proof
            $proof = PaymentProof::create([
                'payment_id' => $payment->id,
                'file_path' => $this->storeFile($file, $payment),
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'notes' => $notes,
            ]);

            return $proof;
        });
    }

    /**
     * Store the receipt file
     *
     * @param  UploadedFile  $file  The file to store
     * @param  Payment  $payment  The payment the file is for
     * @return string The path to the stored file
     */
    private function storeFile(UploadedFile $file, Payment $payment): string
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = "payment_{$payment->id}_receipt_".time().".{$extension}";

        return $file->storeAs('receipts', $fileName, 'public');
    }
}
