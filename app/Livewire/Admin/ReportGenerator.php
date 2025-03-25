<?php

namespace App\Livewire\Admin;

use App\Actions\Admin\GenerateReportAction;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class ReportGenerator extends Component
{
    use WithPagination;

    public string $reportType = 'payment-summary';
    public string $dateRange = 'this-month';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $status = null;
    public ?string $paymentType = null;
    public string $exportFormat = 'csv';
    public bool $isGenerating = false;
    public ?string $generatedFilePath = null;
    public ?string $generatedFileName = null;
    public Collection $reportData;

    protected $queryString = ['reportType', 'dateRange', 'startDate', 'endDate', 'status', 'paymentType'];

    public function mount()
    {
        $this->setDefaultDates();
        $this->reportData = collect();
    }

    public function setDefaultDates()
    {
        $now = Carbon::now();
        
        switch ($this->dateRange) {
            case 'today':
                $this->startDate = $now->format('Y-m-d');
                $this->endDate = $now->format('Y-m-d');
                break;
            case 'this-week':
                $this->startDate = $now->startOfWeek()->format('Y-m-d');
                $this->endDate = $now->endOfWeek()->format('Y-m-d');
                break;
            case 'this-month':
                $this->startDate = $now->startOfMonth()->format('Y-m-d');
                $this->endDate = $now->endOfMonth()->format('Y-m-d');
                break;
            case 'this-year':
                $this->startDate = $now->startOfYear()->format('Y-m-d');
                $this->endDate = $now->endOfYear()->format('Y-m-d');
                break;
            case 'last-month':
                $lastMonth = $now->subMonth();
                $this->startDate = $lastMonth->startOfMonth()->format('Y-m-d');
                $this->endDate = $lastMonth->endOfMonth()->format('Y-m-d');
                break;
            case 'custom':
                // Keep the existing dates if they're set
                if (!$this->startDate) {
                    $this->startDate = $now->subDays(30)->format('Y-m-d');
                }
                if (!$this->endDate) {
                    $this->endDate = $now->format('Y-m-d');
                }
                break;
        }
    }

    public function updatedDateRange()
    {
        $this->setDefaultDates();
    }

    public function generateReport(GenerateReportAction $reportAction)
    {
        $this->isGenerating = true;
        $this->generatedFilePath = null;
        $this->generatedFileName = null;

        switch ($this->reportType) {
            case 'payment-summary':
                $this->reportData = $reportAction->paymentSummary('custom', $this->startDate, $this->endDate);
                break;
            case 'member-statistics':
                $this->reportData = collect($reportAction->memberStatistics($this->startDate, $this->endDate));
                break;
            case 'payment-history':
                $this->reportData = $reportAction->paymentHistory(
                    $this->startDate,
                    $this->endDate,
                    $this->status,
                    $this->paymentType
                );
                break;
        }

        $this->isGenerating = false;
    }

    public function exportReport(GenerateReportAction $reportAction)
    {
        if ($this->reportData->isEmpty()) {
            $this->generateReport($reportAction);
        }

        $filename = $this->reportType . '-' . Carbon::now()->format('Y-m-d-His');
        $this->generatedFileName = $filename . '.' . $this->exportFormat;

        if ($this->exportFormat === 'csv') {
            $headers = $this->getReportHeaders();
            $this->generatedFilePath = $reportAction->exportToCsv($this->reportData, $headers, $filename);
        } else {
            $view = 'reports.' . $this->reportType;
            $extraData = [
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
                'generatedAt' => Carbon::now()->format('Y-m-d H:i:s'),
            ];
            $this->generatedFilePath = $reportAction->exportToPdf($this->reportData, $view, $filename, $extraData);
        }

        $this->dispatch('reportGenerated', [
            'filePath' => str_replace(storage_path('app/public'), '/storage', $this->generatedFilePath),
            'fileName' => $this->generatedFileName
        ]);
    }

    private function getReportHeaders(): array
    {
        return match ($this->reportType) {
            'payment-summary' => ['Payment Type', 'Status', 'Total Payments', 'Total Amount'],
            'member-statistics' => ['Metric', 'Value'],
            'payment-history' => ['ID', 'Member', 'Amount', 'Payment Method', 'Payment Type', 'Status', 'Date'],
            default => [],
        };
    }

    public function render()
    {
        return view('livewire.admin.report-generator', [
            'paymentStatuses' => PaymentStatus::cases(),
            'paymentTypes' => PaymentType::cases(),
        ]);
    }
} 