<?php

namespace App\Actions\Admin;

use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateReportAction
{
    /**
     * Execute the report generation based on type and parameters
     *
     * @param  string  $type  The type of report to generate
     * @param  array  $params  Parameters for the report
     * @return object The generated report object
     *
     * @throws \InvalidArgumentException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function execute(string $type, array $params): object
    {
        // Check if user is admin
        if (!Auth::check() || !Auth::user()->is_admin) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Only administrators can generate reports.');
        }

        // Validate report type
        if (!in_array($type, ['payment_summary', 'member_statistics', 'payment_history'])) {
            throw new \InvalidArgumentException("Invalid report type: {$type}");
        }

        // Validate format
        $format = $params['format'] ?? 'pdf';
        if (!in_array($format, ['pdf', 'csv', 'excel'])) {
            throw new \InvalidArgumentException("Invalid format: {$format}");
        }

        // Validate date parameters
        if (isset($params['start_date']) && isset($params['end_date'])) {
            $startDate = Carbon::parse($params['start_date']);
            $endDate = Carbon::parse($params['end_date']);

            if ($startDate->isAfter($endDate)) {
                throw new \InvalidArgumentException('Start date cannot be after end date');
            }
        }

        // Generate report data based on type
        $data = $this->generateReportData($type, $params);

        // Generate file based on format
        $filePath = $this->generateFile($type, $format, $data, $params);

        // Create and return report object
        return (object) [
            'id' => Str::uuid(),
            'type' => $type,
            'format' => $format,
            'file_path' => $filePath,
            'file_name' => $this->getFileName($type, $format, $params),
            'generated_at' => now(),
            'data' => $this->getReportSummary($type, $data),
        ];
    }

    /**
     * Generate the report data based on type and parameters
     *
     * @param  string  $type
     * @param  array  $params
     * @return Collection|array
     */
    private function generateReportData(string $type, array $params)
    {
        return match ($type) {
            'payment_summary' => $this->paymentSummary(
                $params['period'] ?? 'monthly',
                $params['start_date'] ?? null,
                $params['end_date'] ?? null
            ),
            'member_statistics' => $this->memberStatistics(
                $params['start_date'] ?? null,
                $params['end_date'] ?? null
            ),
            'payment_history' => $this->paymentHistory(
                $params['start_date'] ?? null,
                $params['end_date'] ?? null,
                $params['status'] ?? null,
                $params['payment_type'] ?? null
            ),
            default => throw new \InvalidArgumentException("Invalid report type: {$type}")
        };
    }

    /**
     * Generate the report file
     *
     * @param  string  $type
     * @param  string  $format
     * @param  Collection|array  $data
     * @param  array  $params
     * @return string
     */
    private function generateFile(string $type, string $format, $data, array $params): string
    {
        $fileName = $this->getFileName($type, $format, $params);

        if ($format === 'csv') {
            return $this->exportToCsv(
                $data instanceof Collection ? $data : collect($data),
                $this->getHeaders($type),
                Str::slug($fileName, '_')
            );
        } elseif ($format === 'excel') {
            // For now, just use CSV for excel format in testing
            return $this->exportToCsv(
                $data instanceof Collection ? $data : collect($data),
                $this->getHeaders($type),
                Str::slug($fileName, '_')
            );
        } else {
            return $this->exportToPdf(
                $data instanceof Collection ? $data : collect($data),
                "reports.{$type}",
                Str::slug($fileName, '_'),
                $params
            );
        }
    }

    /**
     * Get the headers for CSV/Excel export
     *
     * @param  string  $type
     * @return array
     */
    private function getHeaders(string $type): array
    {
        return match ($type) {
            'payment_summary' => ['Payment Type', 'Status', 'Count', 'Total Amount'],
            'member_statistics' => ['Metric', 'Value'],
            'payment_history' => ['ID', 'User', 'Amount', 'Type', 'Method', 'Status', 'Date'],
            default => []
        };
    }

    /**
     * Get the report file name
     *
     * @param  string  $type
     * @param  string  $format
     * @param  array  $params
     * @return string
     */
    private function getFileName(string $type, string $format, array $params): string
    {
        $datePart = '';

        if ($type === 'member_statistics') {
            $datePart = isset($params['year']) ? $params['year'] : date('Y');
        } else {
            $datePart = date('Y-m-d');
        }

        $extension = match ($format) {
            'pdf' => 'pdf',
            'csv' => 'csv',
            'excel' => 'xlsx',
            default => 'txt'
        };

        return "{$type}_{$datePart}.{$extension}";
    }

    /**
     * Get summary data for report
     *
     * @param  string  $type
     * @param  Collection|array  $data
     * @return array
     */
    private function getReportSummary(string $type, $data): array
    {
        if ($type === 'payment_summary') {
            $verified = Payment::where('status', 'verified')->count();
            $pending = Payment::where('status', 'pending')->count();
            $rejected = Payment::where('status', 'rejected')->count();
            $total = Payment::sum('amount');

            return [
                'verified_count' => $verified,
                'pending_count' => $pending,
                'rejected_count' => $rejected,
                'total_amount' => $total,
            ];
        }

        return [];
    }

    /**
     * Generate a payment summary report for a specific period.
     *
     * @param  string  $period  'daily', 'weekly', 'monthly', 'yearly'
     * @param  string|null  $startDate
     * @param  string|null  $endDate
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
     * @param  string|null  $startDate
     * @param  string|null  $endDate
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
     * @param  string|null  $startDate
     * @param  string|null  $endDate
     * @param  string|null  $status
     * @param  string|null  $paymentType
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
     * @param  Collection  $data
     * @param  array  $headers
     * @param  string  $filename
     * @return string
     */
    private function exportToCsv(Collection $data, array $headers, string $filename): string
    {
        $filepath = "reports/{$filename}.csv";
        $file = fopen(storage_path("app/public/{$filepath}"), 'w');

        // Add headers
        fputcsv($file, $headers);

        // Add data rows
        foreach ($data as $row) {
            if (is_array($row)) {
                fputcsv($file, $row);
            } elseif (is_object($row)) {
                $flatRow = [];
                foreach ((array) $row as $value) {
                    if (is_array($value) || is_object($value)) {
                        $flatRow[] = json_encode($value);
                    } else {
                        $flatRow[] = $value;
                    }
                }
                fputcsv($file, $flatRow);
            }
        }

        fclose($file);

        return $filepath;
    }

    /**
     * Export report data to PDF.
     *
     * @param  Collection  $data
     * @param  string  $view
     * @param  string  $filename
     * @param  array  $extraData
     * @return string
     */
    private function exportToPdf(Collection $data, string $view, string $filename, array $extraData = []): string
    {
        $viewName = str_replace('reports.', '', $view);
        $viewName = Str::kebab($viewName);

        return Storage::disk('public')->put(
            "reports/{$filename}.pdf",
            view("reports.{$viewName}", [
                'data' => $data,
                'extraData' => $extraData,
                'generatedAt' => now(),
            ])->render()
        ) ? "reports/{$filename}.pdf" : '';
    }
}
