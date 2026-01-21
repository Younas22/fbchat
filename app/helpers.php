<?php

use App\Services\SettingsService;

if (!function_exists('settings')) {
    /**
     * Get a setting value by key with fallback to env/config.
     *
     * @param string $key The setting key
     * @param mixed $default Default value if not found
     * @return mixed
     */
    function settings(string $key, $default = null)
    {
        return SettingsService::get($key, $default);
    }
}
