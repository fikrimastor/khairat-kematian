<?php

namespace App\Http\Controllers\Member;

use App\Actions\Members\RegisterMemberAction;
use App\Actions\Members\UpdateMemberAction;
use App\Actions\Members\ChangePasswordAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class MemberController extends Controller
{
    /**
     * Display the registration form.
     */
    public function create(): View
    {
        return view('member.create');
    }

    /**
     * Display the member's profile page.
     */
    public function show(): View
    {
        $user = Auth::user();
        
        return view('member.show', [
            'user' => $user,
            'dependents' => $user->dependents,
        ]);
    }

    /**
     * Display the edit form.
     */
    public function edit(): View
    {
        return view('member.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the member's profile.
     */
    public function update(Request $request, UpdateMemberAction $updateMember)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'identification_number' => 'nullable|string|max:20',
        ]);

        $updateMember->execute(Auth::id(), $validated);

        return redirect()->route('member.show')
            ->with('status', __('Profile updated successfully'));
    }

    /**
     * Display the change password form.
     */
    public function showChangePasswordForm(): View
    {
        return view('member.change-password');
    }

    /**
     * Change the user's password.
     */
    public function changePassword(Request $request, ChangePasswordAction $changePassword)
    {
        $validated = $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    $fail(__('The current password is incorrect.'));
                }
            }],
            'password' => 'required|string|min:8|confirmed',
        ]);

        $changePassword->execute(Auth::id(), $validated);

        return redirect()->route('member.show')
            ->with('status', __('Password changed successfully.'));
    }
} 