<?php

namespace App\Livewire\Admin;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    public $timeframe = 'month';

    public $year;

    public $month;

    public function mount()
    {
        $this->year = now()->year;
        $this->month = now()->month;
    }

    public function render()
    {
        // Get summary statistics
        $totalMembers = User::where('is_admin', false)->count();
        $activeMembers = User::where('is_admin', false)
            ->where('is_active', true)
            ->whereNotNull('membership_expires_at')
            ->where('membership_expires_at', '>=', now())
            ->count();

        // Get payment statistics
        $pendingPayments = Payment::where('status', PaymentStatus::PENDING)->count();
        $verifiedPayments = Payment::where('status', PaymentStatus::VERIFIED)->count();

        // Get revenue statistics
        $totalRevenue = Payment::where('status', PaymentStatus::VERIFIED)->sum('amount');

        // Get revenue by time period
        $revenueQuery = Payment::where('status', PaymentStatus::VERIFIED);

        if ($this->timeframe === 'month') {
            $revenueQuery->whereMonth('created_at', $this->month)
                ->whereYear('created_at', $this->year);
        } elseif ($this->timeframe === 'year') {
            $revenueQuery->whereYear('created_at', $this->year);
        }

        $periodRevenue = $revenueQuery->sum('amount');

        // Get recent payments
        $recentPayments = Payment::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Get monthly payment trends
        $monthlyTrends = Payment::where('status', PaymentStatus::VERIFIED)
            ->whereYear('created_at', $this->year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month')
            ->map(function ($item) {
                return [
                    'month' => date('F', mktime(0, 0, 0, $item->month, 1)),
                    'total' => $item->total,
                ];
            });

        // Get system settings
        $registrationFee = SystemSetting::get('registration_fee', 50);
        $renewalFee = SystemSetting::get('renewal_fee', 40);

        return view('livewire.admin.dashboard', [
            'totalMembers' => $totalMembers,
            'activeMembers' => $activeMembers,
            'pendingPayments' => $pendingPayments,
            'verifiedPayments' => $verifiedPayments,
            'totalRevenue' => $totalRevenue,
            'periodRevenue' => $periodRevenue,
            'recentPayments' => $recentPayments,
            'monthlyTrends' => $monthlyTrends,
            'registrationFee' => $registrationFee,
            'renewalFee' => $renewalFee,
        ]);
    }

    public function setTimeframe($timeframe)
    {
        $this->timeframe = $timeframe;
    }
}
