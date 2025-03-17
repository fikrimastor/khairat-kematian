<?php

namespace App\Providers;

use App\Services\Settings\Contracts\SettingsManagerInterface;
use App\Services\Settings\Factories\SettingsManagerFactory;
use App\Services\Settings\Managers\CacheSettingsManager;
use App\Services\Settings\Managers\DatabaseSettingsManager;
use App\Services\Settings\Managers\FileSettingsManager;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the settings manager factory
        $this->app->singleton(SettingsManagerFactory::class, function ($app) {
            return new SettingsManagerFactory;
        });

        // Register the settings managers
        $this->app->singleton(DatabaseSettingsManager::class, function ($app) {
            return new DatabaseSettingsManager;
        });

        $this->app->singleton(CacheSettingsManager::class, function ($app) {
            return new CacheSettingsManager(
                $app->make(DatabaseSettingsManager::class)
            );
        });

        $this->app->singleton(FileSettingsManager::class, function ($app) {
            return new FileSettingsManager;
        });

        // Register the default settings manager
        $this->app->singleton(SettingsManagerInterface::class, function ($app) {
            $factory = $app->make(SettingsManagerFactory::class);
            $driver = config('settings.driver', 'database');

            return $factory->make($driver);
        });

        // Register the settings facade
        $this->app->bind('settings', function ($app) {
            return $app->make(SettingsManagerInterface::class);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
