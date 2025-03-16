<?php

namespace App\Livewire\Payment;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PaymentList extends Component
{
    use WithFileUploads;
    use WithPagination;

    // Upload Receipt Modal Properties
    public $showUploadModal = false;

    public $selectedPaymentId;

    public $receiptFile;

    public $receiptNotes;

    // Filters
    public $search = '';

    public $status = '';

    public $paymentMethod = '';

    public $paymentType = '';

    public $year = '';

    public $month = '';

    // View Mode
    public $viewMode = 'list'; // 'list' or 'history'

    // Sorting
    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    // Query string parameters for shareable URLs
    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'paymentMethod' => ['except' => ''],
        'paymentType' => ['except' => ''],
        'year' => ['except' => ''],
        'month' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'viewMode' => ['except' => 'list'],
    ];

    protected $listeners = [
        'payment-filter-applied' => '$refresh',
        'receipt-uploaded' => '$refresh',
    ];

    public function mount()
    {
        // Set default year to current year if not specified
        if (empty($this->year)) {
            $this->year = date('Y');
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'status', 'paymentMethod', 'paymentType', 'month']);
        $this->year = date('Y');
        $this->resetPage();

        // Also clear session filters
        session()->forget([
            'payment_filter.status',
            'payment_filter.type',
            'payment_filter.dateFrom',
            'payment_filter.dateTo',
        ]);
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'list' ? 'history' : 'list';
    }

    // Reset page when filters change
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingPaymentMethod()
    {
        $this->resetPage();
    }

    public function updatingPaymentType()
    {
        $this->resetPage();
    }

    public function updatingYear()
    {
        $this->resetPage();
    }

    public function updatingMonth()
    {
        $this->resetPage();
    }

    // Receipt Upload Methods
    public function uploadReceipt($paymentId)
    {
        $this->selectedPaymentId = $paymentId;
        $this->showUploadModal = true;
    }

    public function closeUploadModal()
    {
        $this->showUploadModal = false;
        $this->reset(['selectedPaymentId', 'receiptFile', 'receiptNotes']);
    }

    public function submitReceipt()
    {
        $this->validate([
            'receiptFile' => 'required|file|max:2048|mimes:jpg,jpeg,png,pdf',
            'receiptNotes' => 'nullable|string|max:255',
        ]);

        $payment = Payment::findOrFail($this->selectedPaymentId);

        // Ensure the user can only upload receipts for their own payments
        if ($payment->user_id !== Auth::id()) {
            $this->addError('receiptFile', 'You are not authorized to upload a receipt for this payment.');

            return;
        }

        try {
            app(\App\Actions\Payment\UploadReceiptAction::class)->execute(
                $payment,
                $this->receiptFile,
                $this->receiptNotes
            );

            $this->showUploadModal = false;
            $this->reset(['selectedPaymentId', 'receiptFile', 'receiptNotes']);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('Receipt uploaded successfully. Your payment is pending verification.'),
            ]);
        } catch (\Exception $e) {
            $this->addError('receiptFile', 'Failed to upload receipt: '.$e->getMessage());
        }
    }

    // Computed Properties
    public function getPaymentsProperty()
    {
        $user = Auth::user();
        $query = Payment::query()->when(!$user->is_admin, function ($query) use ($user) {
            return $query->where('user_id', $user->id);
        });

        // Apply filters based on view mode
        if ($this->viewMode === 'history') {
            // Advanced filtering for history view
            $query->when($this->search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('reference_no', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            })
                ->when($this->status, function ($query, $status) {
                    return $query->where('status', $status);
                })
                ->when($this->paymentMethod, function ($query, $method) {
                    return $query->where('payment_method', $method);
                })
                ->when($this->paymentType, function ($query, $type) {
                    return $query->where('payment_type', $type);
                })
                ->when($this->year, function ($query, $year) {
                    return $query->where('year', $year);
                })
                ->when($this->month, function ($query, $month) {
                    return $query->where('month', $month);
                });
        } else {
            // Simple filtering for list view (from session)
            if (session()->has('payment_filter.status') && session('payment_filter.status') !== '') {
                $query->where('status', session('payment_filter.status'));
            }

            if (session()->has('payment_filter.type') && session('payment_filter.type') !== '') {
                $query->where('payment_type', session('payment_filter.type'));
            }

            if (session()->has('payment_filter.dateFrom') && session('payment_filter.dateFrom') !== '') {
                $query->whereDate('created_at', '>=', session('payment_filter.dateFrom'));
            }

            if (session()->has('payment_filter.dateTo') && session('payment_filter.dateTo') !== '') {
                $query->whereDate('created_at', '<=', session('payment_filter.dateTo'));
            }
        }

        return $query->orderBy($this->sortField, $this->sortDirection)->paginate(10);
    }

    public function getStatusesProperty()
    {
        return PaymentStatus::cases();
    }

    public function getPaymentMethodsProperty()
    {
        return PaymentMethod::cases();
    }

    public function getPaymentTypesProperty()
    {
        return PaymentType::cases();
    }

    public function getYearsProperty()
    {
        $currentYear = (int) date('Y');
        $years = [];

        // Include the last 5 years and the next year
        for ($i = $currentYear - 5; $i <= $currentYear + 1; $i++) {
            $years[$i] = $i;
        }

        return $years;
    }

    public function getMonthsProperty()
    {
        return [
            '01' => __('January'),
            '02' => __('February'),
            '03' => __('March'),
            '04' => __('April'),
            '05' => __('May'),
            '06' => __('June'),
            '07' => __('July'),
            '08' => __('August'),
            '09' => __('September'),
            '10' => __('October'),
            '11' => __('November'),
            '12' => __('December'),
        ];
    }

    public function render()
    {
        return view('livewire.payment.payment-list');
    }
}
