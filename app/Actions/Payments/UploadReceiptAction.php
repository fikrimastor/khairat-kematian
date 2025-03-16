<?php

namespace App\Actions\Payments;

use App\Enums\PaymentMethod;
use App\Models\Payment;
use App\Models\PaymentProof;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UploadReceiptAction
{
    /**
     * Upload a receipt for a payment and update the payment record.
     *
     * @param  Payment  $payment  The payment to upload a receipt for
     * @param  UploadedFile  $file  The uploaded receipt file
     * @return array Result of the upload process
     */
    public function execute(Payment $payment, UploadedFile $file): array
    {
        try {
            // Check if payment method requires receipt
            if ($payment->payment_method !== PaymentMethod::BankTransfer) {
                return [
                    'success' => false,
                    'message' => 'Receipt upload not required for this payment method',
                ];
            }

            // Check if file is an image
            if (!$file->isValid() || !Str::startsWith($file->getMimeType(), 'image/')) {
                return [
                    'success' => false,
                    'message' => 'Invalid receipt file. Please upload an image.',
                ];
            }

            // Generate filename
            $filename = 'payment_'.$payment->id.'_'.Str::random(10).'.'.$file->extension();

            // Store file
            $path = $file->storeAs('receipts/uploads', $filename, 'public');

            // Create payment proof record
            PaymentProof::create([
                'payment_id' => $payment->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);

            // Update payment with receipt information
            $payment->update([
                'notes' => $payment->notes."\nReceipt uploaded: ".now()->format('d-m-Y H:i'),
            ]);

            Log::info('Receipt uploaded for payment', [
                'payment_id' => $payment->id,
                'file_path' => $path,
            ]);

            return [
                'success' => true,
                'message' => 'Receipt uploaded successfully',
                'path' => $path,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to upload receipt', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to upload receipt: '.$e->getMessage(),
            ];
        }
    }
}
