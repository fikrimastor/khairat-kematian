<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index(): View
    {
        return view('admin.reports');
    }

    /**
     * Show the payment summary report.
     */
    public function paymentSummary(): View
    {
        return view('admin.reports.payment-summary');
    }

    /**
     * Show the member statistics report.
     */
    public function memberStatistics(): View
    {
        return view('admin.reports.member-statistics');
    }

    /**
     * Show the payment history report.
     */
    public function paymentHistory(): View
    {
        return view('admin.reports.payment-history');
    }
} 