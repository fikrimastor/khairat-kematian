<?php

namespace App\Http\Controllers\Payment;

use App\Actions\Payments\GenerateReceiptAction;
use App\Http\Controllers\Controller;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the receipts.
     */
    public function index(Request $request)
    {
        $receipts = Receipt::with('payment.user')
            ->when($request->user()->cannot('viewAny', Receipt::class), function ($query) use ($request) {
                return $query->whereHas('payment', function ($q) use ($request) {
                    $q->where('user_id', $request->user()->id);
                });
            })
            ->latest()
            ->paginate(10);
            
        return view('receipts.index', compact('receipts'));
    }
    
    /**
     * Display the specified receipt.
     */
    public function show(Receipt $receipt)
    {
        Gate::authorize('view', $receipt);
        
        return view('receipts.show', compact('receipt'));
    }
    
    /**
     * Generate a receipt for a payment.
     */
    public function generate(Request $request, int $paymentId, GenerateReceiptAction $generateReceipt)
    {
        Gate::authorize('generate', Receipt::class);
        
        try {
            $receipt = $generateReceipt->execute($paymentId);
            
            return redirect()
                ->route('receipts.show', $receipt)
                ->with('success', __('Receipt generated successfully.'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Download the receipt PDF.
     */
    public function download(Receipt $receipt)
    {
        Gate::authorize('view', $receipt);
        
        if (!$receipt->receipt_path) {
            return back()->withErrors(['error' => __('Receipt file not found.')]);
        }
        
        if (!Storage::disk('public')->exists($receipt->receipt_path)) {
            return back()->withErrors(['error' => __('Receipt file not found.')]);
        }
        
        return response()->download(
            Storage::disk('public')->path($receipt->receipt_path),
            'receipt_' . $receipt->receipt_number . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
} 