<?php

namespace App\Livewire\Payment;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Livewire\Component;

class PaymentFilter extends Component
{
    public $status = '';

    public $type = '';

    public $dateFrom = '';

    public $dateTo = '';

    public function mount()
    {
        $this->status = session('payment_filter.status', '');
        $this->type = session('payment_filter.type', '');
        $this->dateFrom = session('payment_filter.dateFrom', '');
        $this->dateTo = session('payment_filter.dateTo', '');
    }

    public function apply()
    {
        session([
            'payment_filter.status' => $this->status,
            'payment_filter.type' => $this->type,
            'payment_filter.dateFrom' => $this->dateFrom,
            'payment_filter.dateTo' => $this->dateTo,
        ]);

        $this->dispatch('payment-filter-applied');
        $this->dispatch('closeModal');
    }

    public function resetFilters()
    {
        $this->reset(['status', 'type', 'dateFrom', 'dateTo']);

        session()->forget([
            'payment_filter.status',
            'payment_filter.type',
            'payment_filter.dateFrom',
            'payment_filter.dateTo',
        ]);

        $this->dispatch('payment-filter-applied');
        $this->dispatch('closeModal');
    }

    public function render()
    {
        return view('livewire.payment.payment-filter', [
            'statuses' => PaymentStatus::cases(),
            'types' => PaymentType::cases(),
        ]);
    }
}
