<?php

namespace App\Http\Livewire\Payment;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentList extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $methodFilter = '';

    public $yearFilter = '';

    public $monthFilter = '';

    public $perPage = 10;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $isAdmin;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'methodFilter' => ['except' => ''],
        'yearFilter' => ['except' => ''],
        'monthFilter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected $listeners = [
        'paymentCreated' => '$refresh',
        'paymentStatusUpdated' => '$refresh',
    ];

    /**
     * Component mount method.
     */
    public function mount()
    {
        $this->isAdmin = Auth::user()->hasRole(['Super Admin', 'Administrator', 'Treasurer']);

        // Set default year filter to current year
        if (empty($this->yearFilter)) {
            $this->yearFilter = now()->year;
        }
    }

    /**
     * Sort results by the given field.
     *
     * @param  mixed  $field
     */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Reset all filters.
     */
    public function resetFilters()
    {
        $this->reset(['search', 'statusFilter', 'methodFilter', 'monthFilter']);
        $this->yearFilter = now()->year;
    }

    /**
     * Get list of available months.
     */
    public function getMonthsProperty()
    {
        return [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December',
        ];
    }

    /**
     * Get list of available years.
     */
    public function getYearsProperty()
    {
        $currentYear = now()->year;

        return range($currentYear - 5, $currentYear + 1);
    }

    /**
     * Get list of available payment methods.
     */
    public function getPaymentMethodsProperty()
    {
        return PaymentMethod::toArray();
    }

    /**
     * Get list of available payment statuses.
     */
    public function getPaymentStatusesProperty()
    {
        return collect(PaymentStatus::cases())->map(function ($status) {
            return [
                'value' => $status->value,
                'label' => $status->label(),
            ];
        })->toArray();
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $query = Payment::query()
            ->with(['user', 'receipt'])
            ->when(!$this->isAdmin, function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->whereHas('user', function ($user) {
                        $user->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('email', 'like', '%'.$this->search.'%');
                    })
                        ->orWhere('reference_no', 'like', '%'.$this->search.'%')
                        ->orWhereHas('receipt', function ($receipt) {
                            $receipt->where('receipt_number', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->statusFilter, function ($query) {
                return $query->where('status', $this->statusFilter);
            })
            ->when($this->methodFilter, function ($query) {
                return $query->where('payment_method', $this->methodFilter);
            })
            ->when($this->yearFilter, function ($query) {
                return $query->where('year', $this->yearFilter);
            })
            ->when($this->monthFilter, function ($query) {
                return $query->where('month', $this->monthFilter);
            });

        return view('livewire.payment.payment-list', [
            'payments' => $query->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage),
        ]);
    }
}
