<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed get(string $key, $default = null)
 * @method static mixed set(string $key, $value, ?string $description = null)
 * @method static bool has(string $key)
 * @method static bool forget(string $key)
 * @method static array all()
 *
 * @see \App\Services\Settings\Contracts\SettingsManagerInterface
 */
class Settings extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'settings';
    }
}
