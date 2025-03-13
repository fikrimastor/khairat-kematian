<?php

namespace App\Http\Controllers\Payment;

use App\Actions\Payments\GetPaymentDetailsAction;
use App\Actions\Payments\VerifyPaymentAction;
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
        // Check if user is admin
        $isAdmin = Auth::user()->hasRole(['Super Admin', 'Administrator', 'Treasurer']);

        // Get payments based on user role
        $payments = $isAdmin
            ? Payment::with(['user', 'receipt'])->latest()->paginate(15)
            : Payment::with(['receipt'])
                ->where('user_id', Auth::id())
                ->latest()
                ->paginate(15);

        return view('payment.index', compact('payments', 'isAdmin'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create()
    {
        // We'll use Livewire for the form, so just return the view
        return view('payment.create');
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment, GetPaymentDetailsAction $getPaymentDetailsAction)
    {
        // Check if user can view this payment
        if (Auth::id() !== $payment->user_id && !Auth::user()->hasPermissionTo('payment.view')) {
            abort(403, 'Unauthorized action.');
        }

        // Get detailed payment information
        $paymentDetails = $getPaymentDetailsAction->execute($payment->id);

        if (!$paymentDetails['success']) {
            return back()->with('error', $paymentDetails['message']);
        }

        return view('payment.show', [
            'payment' => $payment,
            'details' => $paymentDetails['data'],
        ]);
    }

    /**
     * Process payment verification.
     */
    public function verify(Request $request, Payment $payment, VerifyPaymentAction $verifyPaymentAction)
    {
        // Check if user can verify payments
        if (!Auth::user()->hasPermissionTo('payment.verify')) {
            abort(403, 'Unauthorized action.');
        }

        // Validate request
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:verified,rejected'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Verify payment
        $result = $verifyPaymentAction->execute(
            $payment->id,
            $validated['status'],
            $validated['notes'] ?? null
        );

        if ($result['success']) {
            return redirect()->route('payments.show', $payment)
                ->with('success', $result['message']);
        } else {
            return back()->with('error', $result['message']);
        }
    }

    /**
     * Download receipt for a payment.
     */
    public function downloadReceipt(Payment $payment)
    {
        // Check if user can access this receipt
        if (Auth::id() !== $payment->user_id && !Auth::user()->hasPermissionTo('receipt.download')) {
            abort(403, 'Unauthorized action.');
        }

        // Check if payment has a receipt
        if (!$payment->receipt || !$payment->receipt->receipt_path) {
            return back()->with('error', 'Receipt not available for this payment.');
        }

        // Return the receipt file
        return response()->download(
            storage_path('app/public/'.$payment->receipt->receipt_path),
            'Receipt-'.$payment->receipt->receipt_number.'.pdf'
        );
    }
}
