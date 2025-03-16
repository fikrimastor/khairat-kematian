<?php

namespace App\Livewire\Admin;

use App\Models\SystemSetting;
use Illuminate\Support\Collection;
use Livewire\Component;

class SystemSettingsManager extends Component
{
    /**
     * The settings grouped by category.
     */
    public Collection $settingGroups;

    /**
     * The form data for settings.
     */
    public array $formData = [];

    /**
     * The currently active tab.
     */
    public string $activeTab = 'organization';

    /**
     * The categories for grouping settings.
     */
    protected array $categories = [
        'organization' => [
            'organization_name',
            'organization_address',
            'organization_phone',
            'organization_email',
        ],
        'payment' => [
            'registration_fee',
            'renewal_fee',
            'bank_name',
            'bank_account_name',
            'bank_account_number',
        ],
        'notification' => [
            'email_notifications_enabled',
            'admin_notification_email',
        ],
        'system' => [
            'maintenance_mode',
            'backup_enabled',
            'backup_frequency',
        ],
    ];

    /**
     * Mount the component.
     */
    public function mount()
    {
        $this->loadSettings();
    }

    /**
     * Load all settings from the database.
     */
    protected function loadSettings()
    {
        // Get all settings
        $allSettings = SystemSetting::all()->keyBy('key');

        // Group settings by category
        $this->settingGroups = collect($this->categories)->map(function ($keys, $category) use ($allSettings) {
            return collect($keys)->map(function ($key) use ($allSettings) {
                return $allSettings->get($key) ?? new SystemSetting([
                    'key' => $key,
                    'value' => '',
                    'description' => '',
                ]);
            });
        });

        // Prepare form data
        foreach ($allSettings as $setting) {
            $this->formData[$setting->key] = $setting->value;
        }

        // Add missing settings to form data
        foreach ($this->categories as $category => $keys) {
            foreach ($keys as $key) {
                if (!isset($this->formData[$key])) {
                    $this->formData[$key] = '';
                }
            }
        }
    }

    /**
     * Set the active tab.
     */
    public function setActiveTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    /**
     * Save the settings.
     */
    public function save()
    {
        // Validate the form data
        $rules = [];
        foreach ($this->categories as $category => $keys) {
            foreach ($keys as $key) {
                if (str_contains($key, 'fee') || str_contains($key, 'amount')) {
                    $rules[$key] = 'required|numeric|min:0';
                } elseif (str_contains($key, 'email')) {
                    $rules[$key] = 'nullable|email';
                } elseif (str_contains($key, 'enabled') || str_contains($key, 'mode')) {
                    $rules[$key] = 'boolean';
                } else {
                    $rules[$key] = 'nullable|string';
                }
            }
        }

        $this->validate($rules);

        // Save the settings
        foreach ($this->formData as $key => $value) {
            SystemSetting::set($key, $value);
        }

        // Reload settings to ensure we have the latest data
        $this->loadSettings();

        // Show success message
        session()->flash('success', __('Settings updated successfully.'));
    }

    /**
     * Reset the settings to their default values.
     */
    public function resetToDefaults()
    {
        // Default values for settings
        $defaults = [
            'registration_fee' => '50',
            'renewal_fee' => '40',
            'organization_name' => 'Khairat Kematian Masjid',
            'organization_address' => 'Jalan Masjid, 12345 Bandar, Malaysia',
            'organization_phone' => '+60123456789',
            'organization_email' => 'info@khairat-kematian.org',
            'bank_account_name' => 'Khairat Kematian Masjid',
            'bank_account_number' => '1234567890',
            'bank_name' => 'Bank Islam Malaysia',
            'email_notifications_enabled' => '1',
            'admin_notification_email' => 'admin@khairat-kematian.org',
            'maintenance_mode' => '0',
            'backup_enabled' => '1',
            'backup_frequency' => 'daily',
        ];

        // Update form data with defaults
        foreach ($defaults as $key => $value) {
            $this->formData[$key] = $value;
        }

        // Save the settings
        $this->save();

        session()->flash('success', __('Settings reset to defaults.'));
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.system-settings-manager');
    }
}
