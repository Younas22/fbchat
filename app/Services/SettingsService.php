<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SettingsService
{
    /**
     * Cache key for settings.
     */
    protected const CACHE_KEY = 'app_settings';

    /**
     * Cache duration in seconds (1 hour).
     */
    protected const CACHE_DURATION = 3600;

    /**
     * Mapping of setting keys to their env/config equivalents.
     */
    protected static array $envMapping = [
        'FACEBOOK_APP_ID' => 'services.facebook.app_id',
        'FACEBOOK_APP_SECRET' => 'services.facebook.app_secret',
        'FACEBOOK_GRAPH_API_VERSION' => 'services.facebook.graph_version',
        'FACEBOOK_BUSINESS_ACCOUNT_TOKEN' => 'services.facebook.business_token',
        'FACEBOOK_WEBHOOK_VERIFY_TOKEN' => 'services.facebook.verify_token',
        'APP_URL' => 'app.url',
    ];

    /**
     * Get a setting value by key with fallback to env/config.
     */
    public static function get(string $key, $default = null)
    {
        // Check if settings table exists (for fresh installations)
        if (!Schema::hasTable('settings')) {
            return self::getFromConfig($key, $default);
        }

        $settings = self::getAllCached();

        if (isset($settings[$key])) {
            return $settings[$key];
        }

        // Fallback to config/env value
        return self::getFromConfig($key, $default);
    }

    /**
     * Get value from config/env.
     */
    protected static function getFromConfig(string $key, $default = null)
    {
        if (isset(self::$envMapping[$key])) {
            return config(self::$envMapping[$key], $default);
        }

        return $default;
    }

    /**
     * Get all settings as key-value pairs (cached).
     */
    public static function getAllCached(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            if (!Schema::hasTable('settings')) {
                return [];
            }

            $settings = Setting::all();
            $result = [];

            foreach ($settings as $setting) {
                $result[$setting->key] = $setting->value;
            }

            return $result;
        });
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, $value): bool
    {
        $setting = Setting::where('key', $key)->first();

        if ($setting) {
            $setting->value = $value;
            $setting->save();
            self::clearCache();
            return true;
        }

        return false;
    }

    /**
     * Bulk update settings.
     */
    public static function bulkUpdate(array $settings): bool
    {
        foreach ($settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                // Use the model's setter which handles encryption automatically
                $setting->value = $value;
                $setting->save();
            }
        }

        self::clearCache();
        return true;
    }

    /**
     * Clear the settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get all settings grouped by group field.
     */
    public static function getAllGrouped(): array
    {
        return Setting::getAllGrouped();
    }

    /**
     * Get the webhook URL based on APP_URL setting.
     */
    public static function getWebhookUrl(): string
    {
        $appUrl = self::get('APP_URL', config('app.url'));
        return rtrim($appUrl, '/') . '/api/webhook/facebook';
    }
}

/**
 * Global helper function for accessing settings.
 */
if (!function_exists('settings')) {
    function settings(string $key, $default = null)
    {
        return SettingsService::get($key, $default);
    }
}
