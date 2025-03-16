<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard(): View
    {
        return view('admin.dashboard');
    }

    /**
     * Display the members management page.
     */
    public function members(): View
    {
        return view('admin.members');
    }

    /**
     * Display the details of a specific member.
     */
    public function memberDetails(User $member): View
    {
        return view('admin.member-details', [
            'member' => $member,
        ]);
    }

    /**
     * Display the reports generation page.
     */
    public function reports(): View
    {
        return view('admin.reports');
    }
}
