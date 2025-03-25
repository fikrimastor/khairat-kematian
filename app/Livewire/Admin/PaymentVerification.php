<?php

namespace App\Livewire\Admin;

use App\Actions\Admin\VerifyPaymentAction;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentVerification extends Component
{
    use WithPagination;

    public $search = '';

    public $status = 'pending';

    public $selectedPayment = null;

    public $verificationNotes = '';

    public $showModal = false;

    protected $queryString = ['search', 'status'];

    protected $listeners = ['refreshPayments' => '$refresh'];

    public function mount()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            throw new \Illuminate\Auth\Access\AuthorizationException('This action is unauthorized.');
        }

        $this->status = 'pending';
    }

    public function selectPayment($paymentId)
    {
        $this->selectedPayment = Payment::with(['user', 'user.dependents', 'proofs'])
            ->findOrFail($paymentId);

        $this->verificationNotes = '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedPayment = null;
        $this->verificationNotes = '';
    }

    public function approve(VerifyPaymentAction $verifyPayment)
    {
        if (!$this->selectedPayment) {
            return;
        }

        $verifyPayment->execute(
            $this->selectedPayment->id,
            Auth::id(),
            true,
            $this->verificationNotes
        );

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Payment has been approved and receipt generated.'),
        ]);

        $this->closeModal();
        $this->dispatch('refreshPayments');
    }

    public function reject(VerifyPaymentAction $verifyPayment)
    {
        if (!$this->selectedPayment) {
            return;
        }

        $this->validate([
            'verificationNotes' => 'required|min:10',
        ], [
            'verificationNotes.required' => 'Please provide a reason for rejection.',
            'verificationNotes.min' => 'The rejection reason must be at least 10 characters.',
        ]);

        $verifyPayment->execute(
            $this->selectedPayment->id,
            Auth::id(),
            false,
            $this->verificationNotes
        );

        $this->dispatch('notify', [
            'type' => 'info',
            'message' => __('Payment has been rejected.'),
        ]);

        $this->closeModal();
        $this->dispatch('refreshPayments');
    }

    public function render()
    {
        $statusValue = $this->status === 'all' ? null : $this->status;

        $payments = Payment::with(['user', 'proofs'])
            ->when($statusValue, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($this->search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('identification_number', 'like', "%{$search}%");
                })
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhere('reference_no', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.payment-verification', [
            'payments' => $payments,
            'statuses' => PaymentStatus::cases(),
        ]);
    }
}
