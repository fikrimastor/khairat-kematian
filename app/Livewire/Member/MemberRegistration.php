<?php

namespace App\Livewire\Member;

use App\Actions\Members\RegisterMemberAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MemberRegistration extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $address = '';
    public $phone = '';
    public $identification_number = '';
    public $language = 'ms'; // Default to Bahasa Malaysia
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|min:8|confirmed',
        'password_confirmation' => 'required',
        'address' => 'nullable|string|max:500',
        'phone' => 'nullable|string|max:20',
        'identification_number' => 'nullable|string|max:20',
        'language' => 'required|in:ms,en',
    ];
    
    public function register(RegisterMemberAction $registerMember)
    {
        $this->validate();
        
        $user = $registerMember->execute([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'address' => $this->address,
            'phone' => $this->phone,
            'identification_number' => $this->identification_number,
            'language' => $this->language,
        ]);
        
        // Log in the user
        Auth::login($user);
        
        // Redirect to dashboard or welcome page
        return redirect()->route('dashboard')
            ->with('status', __('Registration successful! Welcome to Khairat Kematian.'));
    }
    
    public function render()
    {
        return view('livewire.member.member-registration');
    }
} 