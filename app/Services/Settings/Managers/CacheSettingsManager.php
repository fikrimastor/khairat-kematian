<?php

namespace App\Services\Settings\Managers;

use App\Services\Settings\Contracts\SettingsManagerInterface;
use Illuminate\Support\Facades\Cache;

class CacheSettingsManager implements SettingsManagerInterface
{
    /**
     * The cache prefix for settings.
     */
    protected string $prefix = 'settings:';

    /**
     * The cache TTL in seconds.
     */
    protected int $ttl = 86400; // 24 hours

    /**
     * The database settings manager for fallback.
     */
    protected DatabaseSettingsManager $databaseManager;

    /**
     * Create a new cache settings manager instance.
     */
    public function __construct(DatabaseSettingsManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
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
        return Cache::remember($this->prefix.$key, $this->ttl, function () use ($key, $default) {
            return $this->databaseManager->get($key, $default);
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
        $result = $this->databaseManager->set($key, $value, $description);
        Cache::put($this->prefix.$key, $value, $this->ttl);

        return $result;
    }

    /**
     * Check if a setting exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function has(string $key): bool
    {
        if (Cache::has($this->prefix.$key)) {
            return true;
        }

        return $this->databaseManager->has($key);
    }

    /**
     * Remove a setting.
     *
     * @param  string  $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        Cache::forget($this->prefix.$key);

        return $this->databaseManager->forget($key);
    }

    /**
     * Get all settings.
     *
     * @return array
     */
    public function all(): array
    {
        return Cache::remember($this->prefix.'all', $this->ttl, function () {
            return $this->databaseManager->all();
        });
    }
}
