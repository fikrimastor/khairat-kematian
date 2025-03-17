<?php

namespace App\Services\Settings\Contracts;

interface SettingsManagerInterface
{
    /**
     * Get a setting value by key.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Set a setting value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  string|null  $description
     * @return mixed
     */
    public function set(string $key, $value, ?string $description = null);

    /**
     * Check if a setting exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Remove a setting.
     *
     * @param  string  $key
     * @return bool
     */
    public function forget(string $key): bool;

    /**
     * Get all settings.
     *
     * @return array
     */
    public function all(): array;
}
