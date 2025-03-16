<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
     * Display the system settings page.
     */
    public function settings(): View
    {
        return view('admin.settings');
    }

    /**
     * Display the reports generation page.
     */
    public function reports(): View
    {
        return view('admin.reports');
    }
}
