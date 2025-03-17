<?php

namespace App\Services\Settings\Managers;

use App\Models\SystemSetting;
use App\Services\Settings\Contracts\SettingsManagerInterface;
use Illuminate\Support\Facades\Cache;

class DatabaseSettingsManager implements SettingsManagerInterface
{
    /**
     * Get a setting value by key.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return Cache::remember('setting.'.$key, 3600, function () use ($key, $default) {
            $setting = SystemSetting::where('key', $key)->first();

            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  string|null  $description
     * @return mixed
     */
    public function set(string $key, $value, ?string $description = null)
    {
        $setting = SystemSetting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'description' => $description,
            ]
        );

        Cache::forget('setting.'.$key);

        return $setting;
    }

    /**
     * Check if a setting exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return SystemSetting::where('key', $key)->exists();
    }

    /**
     * Remove a setting.
     *
     * @param  string  $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        $result = SystemSetting::where('key', $key)->delete();
        Cache::forget('setting.'.$key);

        return $result > 0;
    }

    /**
     * Get all settings.
     *
     * @return array
     */
    public function all(): array
    {
        return SystemSetting::all()->pluck('value', 'key')->toArray();
    }
}
