<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

class MemberDetails extends Component
{
    public User $member;

    public function mount(User $member)
    {
        $this->member = $member;
    }

    public function render()
    {
        return view('livewire.admin.member-details', [
            'member' => $this->member,
        ]);
    }
}
