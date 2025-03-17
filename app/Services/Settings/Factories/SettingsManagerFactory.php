<?php

namespace App\Services\Settings\Factories;

use App\Services\Settings\Contracts\SettingsManagerInterface;
use App\Services\Settings\Managers\CacheSettingsManager;
use App\Services\Settings\Managers\DatabaseSettingsManager;
use App\Services\Settings\Managers\FileSettingsManager;

class SettingsManagerFactory
{
    /**
     * Create a new settings manager instance.
     *
     * @param  string  $driver  The driver to use (database, cache, file)
     * @return SettingsManagerInterface
     *
     * @throws \InvalidArgumentException
     */
    public function make(string $driver = 'database'): SettingsManagerInterface
    {
        return match ($driver) {
            'database' => app(DatabaseSettingsManager::class),
            'cache' => app(CacheSettingsManager::class),
            'file' => app(FileSettingsManager::class),
            default => throw new \InvalidArgumentException("Unsupported settings driver: {$driver}")
        };
    }
}
