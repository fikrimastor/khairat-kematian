<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\MemberManagementServiceFactory;
use Livewire\Component;
use Livewire\WithPagination;

class MemberManagement extends Component
{
    use WithPagination;

    public $search = '';

    public $status = '';

    public $sortField = 'name';

    public $sortDirection = 'asc';

    public $perPage = 10;

    // For member editing
    public $editingMember = null;

    public $name;

    public $email;

    public $phone;

    public $address;

    public $identification_number;

    public $is_active;

    public $membership_expires_at;

    public $membership_type;

    // For confirmation modals
    public $confirmingMemberDeletion = false;

    public $memberToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:500',
        'identification_number' => 'required|string|max:20',
        'is_active' => 'boolean',
        'membership_expires_at' => 'nullable|date',
        'membership_type' => 'nullable|string|max:50',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function editMember(User $member)
    {
        $this->editingMember = $member;
        $this->name = $member->name;
        $this->email = $member->email;
        $this->phone = $member->phone;
        $this->address = $member->address;
        $this->identification_number = $member->identification_number;
        $this->is_active = $member->is_active;
        $this->membership_expires_at = $member->membership_expires_at ? $member->membership_expires_at->format('Y-m-d') : null;
        $this->membership_type = $member->membership_type;
    }

    public function cancelEdit()
    {
        $this->editingMember = null;
        $this->resetValidation();
        $this->reset(['name', 'email', 'phone', 'address', 'identification_number', 'is_active', 'membership_expires_at', 'membership_type']);
    }

    public function updateMember()
    {
        $this->validate();

        $service = MemberManagementServiceFactory::create();
        $service->updateMember($this->editingMember, [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'identification_number' => $this->identification_number,
            'is_active' => $this->is_active,
            'membership_expires_at' => $this->membership_expires_at,
            'membership_type' => $this->membership_type,
        ]);

        $this->cancelEdit();
        session()->flash('message', 'Member updated successfully.');
    }

    public function confirmMemberDeletion(User $member)
    {
        $this->memberToDelete = $member;
        $this->confirmingMemberDeletion = true;
    }

    public function deleteMember()
    {
        if ($this->memberToDelete) {
            $service = MemberManagementServiceFactory::create();
            $service->deleteMember($this->memberToDelete);

            $this->confirmingMemberDeletion = false;
            $this->memberToDelete = null;
            session()->flash('message', 'Member deleted successfully.');
        }
    }

    public function cancelDelete()
    {
        $this->confirmingMemberDeletion = false;
        $this->memberToDelete = null;
    }

    public function activateMember(User $member)
    {
        $service = MemberManagementServiceFactory::create();
        $service->activateMember($member);
        session()->flash('message', 'Member activated successfully.');
    }

    public function deactivateMember(User $member)
    {
        $service = MemberManagementServiceFactory::create();
        $service->deactivateMember($member);
        session()->flash('message', 'Member deactivated successfully.');
    }

    public function extendMembership(User $member)
    {
        $service = MemberManagementServiceFactory::create();
        $service->extendMembership($member);
        session()->flash('message', 'Membership extended by 1 year.');
    }

    public function render()
    {
        $service = MemberManagementServiceFactory::create();
        $members = $service->getFilteredMembers(
            $this->search,
            $this->status,
            $this->sortField,
            $this->sortDirection,
            $this->perPage
        );

        return view('livewire.admin.member-management', [
            'members' => $members,
        ]);
    }
}
