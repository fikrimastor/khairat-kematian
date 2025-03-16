<?php

namespace App\Http\Controllers\Payment;

use App\Actions\Payment\ActivateMembershipAction;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display a listing of the payments.
     */
    public function index()
    {
        // Return the view that contains our Livewire component
        return view('payment.history');
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create()
    {
        return view('payment.create');
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        // Ensure the user can only view their own payments unless they're an admin
        if ($payment->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        return view('payment.show', compact('payment'));
    }

    /**
     * Verify a payment (admin only).
     */
    public function verify(Request $request, Payment $payment)
    {
        // Ensure only admins can verify payments
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => 'required|in:verified,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        $isVerified = $validated['status'] === 'verified';
        $newStatus = $isVerified ? PaymentStatus::VERIFIED : PaymentStatus::REJECTED;

        $payment->update([
            'status' => $newStatus,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'notes' => $payment->notes."\n".($validated['notes'] ?? ''),
        ]);

        // If payment is verified, activate the user's membership
        if ($isVerified) {
            $activateMembership = new ActivateMembershipAction;
            $activateMembership->execute($payment);
        }

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Payment has been '.$validated['status'].'.');
    }

    /**
     * Download the payment receipt.
     */
    public function downloadReceipt(Payment $payment)
    {
        // Ensure the user can only download their own receipts unless they're an admin
        if ($payment->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        // Check if payment has a receipt
        if (!$payment->paymentProof) {
            abort(404, 'Receipt not found.');
        }

        // Check if the file exists
        $filePath = storage_path('app/public/'.$payment->paymentProof->file_path);
        if (!file_exists($filePath)) {
            abort(404, 'Receipt file not found.');
        }

        return response()->file($filePath, [
            'Content-Disposition' => 'attachment; filename="'.$payment->paymentProof->file_name.'"',
        ]);
    }
}
