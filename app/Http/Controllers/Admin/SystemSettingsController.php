<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingsController extends Controller
{
    /**
     * Display the system settings page.
     */
    public function index()
    {
        return view('admin.settings.index');
    }

    /**
     * Update the specified system setting.
     */
    public function update(Request $request, string $key)
    {
        $request->validate([
            'value' => 'required|string',
        ]);

        $setting = SystemSetting::where('key', $key)->firstOrFail();
        $setting->update(['value' => $request->value]);

        return back()->with('success', __('Setting updated successfully.'));
    }

    /**
     * Bulk update system settings.
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'required|string',
        ]);

        foreach ($request->settings as $key => $value) {
            SystemSetting::set($key, $value);
        }

        return back()->with('success', __('Settings updated successfully.'));
    }
}
