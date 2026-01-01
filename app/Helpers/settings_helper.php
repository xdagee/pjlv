<?php

use App\Services\SettingsService;

if (!function_exists('setting')) {
    /**
     * Get a system setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return app(SettingsService::class)->get($key, $default);
    }
}

if (!function_exists('setting_set')) {
    /**
     * Set a system setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $type
     * @return \App\Models\SystemSetting
     */
    function setting_set(string $key, $value, ?string $type = null)
    {
        return app(SettingsService::class)->set($key, $value, $type);
    }
}
