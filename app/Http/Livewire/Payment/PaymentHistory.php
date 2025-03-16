<?php

namespace App\Http\Livewire\Payment;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentHistory extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $status = '';
    public $paymentMethod = '';
    public $paymentType = '';
    public $year = '';
    public $month = '';
    
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
    ];

    public function mount()
    {
        // Set default year to current year if not specified
        if (empty($this->year)) {
            $this->year = date('Y');
        }
    }

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
    }

    public function render()
    {
        $user = Auth::user();
        
        $query = Payment::query()
            ->where('user_id', $user->id)
            ->when($this->search, function ($query, $search) {
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
            })
            ->orderBy($this->sortField, $this->sortDirection);
        
        $payments = $query->paginate(10);
        
        return view('livewire.payment.payment-history', [
            'payments' => $payments,
            'statuses' => PaymentStatus::cases(),
            'paymentMethods' => PaymentMethod::cases(),
            'paymentTypes' => PaymentType::cases(),
            'years' => $this->getAvailableYears(),
            'months' => $this->getMonths(),
        ]);
    }

    private function getAvailableYears()
    {
        $currentYear = (int) date('Y');
        $years = [];
        
        // Include the last 5 years and the next year
        for ($i = $currentYear - 5; $i <= $currentYear + 1; $i++) {
            $years[$i] = $i;
        }
        
        return $years;
    }

    private function getMonths()
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
} 