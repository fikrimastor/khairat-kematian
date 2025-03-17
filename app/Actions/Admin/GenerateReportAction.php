<?php

namespace App\Actions\Admin;

use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GenerateReportAction
{
    /**
     * Generate a payment summary report for a specific period.
     *
     * @param string $period 'daily', 'weekly', 'monthly', 'yearly'
     * @param string|null $startDate
     * @param string|null $endDate
     * @return Collection
     */
    public function paymentSummary(string $period = 'monthly', ?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = Payment::query()
            ->select(
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as total_payments'),
                DB::raw('payment_type'),
                DB::raw('status')
            )
            ->groupBy('payment_type', 'status');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        } else {
            // Default to current month if no dates provided
            $query->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year);
        }

        return $query->get();
    }

    /**
     * Generate member statistics report.
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function memberStatistics(?string $startDate = null, ?string $endDate = null): array
    {
        $query = User::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        }

        $totalMembers = $query->count();
        $totalDependents = DB::table('dependents')->count();
        $newMembers = User::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $activeMembers = User::whereHas('payments', function ($query) {
            $query->where('status', 'verified')
                ->whereYear('created_at', Carbon::now()->year);
        })->count();

        return [
            'total_members' => $totalMembers,
            'total_dependents' => $totalDependents,
            'new_members' => $newMembers,
            'active_members' => $activeMembers,
            'average_dependents' => $totalMembers > 0 ? round($totalDependents / $totalMembers, 2) : 0,
        ];
    }

    /**
     * Generate payment history report.
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string|null $status
     * @param string|null $paymentType
     * @return Collection
     */
    public function paymentHistory(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $status = null,
        ?string $paymentType = null
    ): Collection {
        $query = Payment::with(['user'])
            ->select('payments.*')
            ->orderBy('created_at', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($paymentType) {
            $query->where('payment_type', $paymentType);
        }

        return $query->get();
    }

    /**
     * Export report data to CSV.
     *
     * @param Collection $data
     * @param array $headers
     * @param string $filename
     * @return string
     */
    public function exportToCsv(Collection $data, array $headers, string $filename): string
    {
        $path = storage_path('app/public/reports');
        
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        
        $filepath = $path . '/' . $filename . '.csv';
        $file = fopen($filepath, 'w');
        
        // Add headers
        fputcsv($file, $headers);
        
        // Add data rows
        foreach ($data as $row) {
            if (is_array($row)) {
                fputcsv($file, $row);
            } elseif (is_object($row)) {
                fputcsv($file, (array) $row);
            }
        }
        
        fclose($file);
        
        return $filepath;
    }

    /**
     * Export report data to PDF.
     *
     * @param Collection $data
     * @param string $view
     * @param string $filename
     * @param array $extraData
     * @return string
     */
    public function exportToPdf(Collection $data, string $view, string $filename, array $extraData = []): string
    {
        $path = storage_path('app/public/reports');
        
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        
        $filepath = $path . '/' . $filename . '.pdf';
        
        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadView($view, array_merge(['data' => $data], $extraData));
        $pdf->save($filepath);
        
        return $filepath;
    }
} 