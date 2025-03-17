<?php

namespace App\Services\Settings\Managers;

use App\Services\Settings\Contracts\SettingsManagerInterface;
use Illuminate\Support\Facades\File;

class FileSettingsManager implements SettingsManagerInterface
{
    /**
     * The path to the settings file.
     */
    protected string $path;

    /**
     * The settings data.
     */
    protected array $settings = [];

    /**
     * Create a new file settings manager instance.
     */
    public function __construct()
    {
        $this->path = storage_path('app/settings.json');
        $this->load();
    }

    /**
     * Load settings from the file.
     */
    protected function load(): void
    {
        if (File::exists($this->path)) {
            $content = File::get($this->path);
            $this->settings = json_decode($content, true) ?? [];
        }
    }

    /**
     * Save settings to the file.
     */
    protected function save(): void
    {
        File::put($this->path, json_encode($this->settings, JSON_PRETTY_PRINT));
    }

    /**
     * Get a setting value by key.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->settings[$key]['value'] ?? $default;
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
        $this->settings[$key] = [
            'value' => $value,
            'description' => $description,
            'updated_at' => now()->toDateTimeString(),
        ];

        $this->save();

        return $this->settings[$key];
    }

    /**
     * Check if a setting exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->settings[$key]);
    }

    /**
     * Remove a setting.
     *
     * @param  string  $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        if (isset($this->settings[$key])) {
            unset($this->settings[$key]);
            $this->save();

            return true;
        }

        return false;
    }

    /**
     * Get all settings.
     *
     * @return array
     */
    public function all(): array
    {
        $result = [];

        foreach ($this->settings as $key => $data) {
            $result[$key] = $data['value'];
        }

        return $result;
    }
}
