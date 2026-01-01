<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    /**
     * Cache TTL in seconds (1 hour)
     */
    protected const CACHE_TTL = 3600;

    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $cacheKey = "setting_{$key}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($key, $default) {
            $setting = SystemSetting::where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            return $setting->casted_value ?? $default;
        });
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $type
     * @return SystemSetting
     */
    public function set(string $key, $value, ?string $type = null): SystemSetting
    {
        $setting = SystemSetting::firstOrNew(['key' => $key]);

        if ($type) {
            $setting->type = $type;
        }

        $setting->setCastedValue($value);
        $setting->save();

        // Clear cache
        Cache::forget("setting_{$key}");

        return $setting;
    }

    /**
     * Get all settings grouped by category
     *
     * @return array
     */
    public function getAllGrouped(): array
    {
        return Cache::remember('settings_all_grouped', self::CACHE_TTL, function () {
            return SystemSetting::all()
                ->groupBy('group')
                ->map(function ($settings) {
                    return $settings->map(function ($setting) {
                        return [
                            'id' => $setting->id,
                            'key' => $setting->key,
                            'value' => $setting->casted_value,
                            'raw_value' => $setting->value,
                            'type' => $setting->type,
                            'label' => $setting->label,
                            'description' => $setting->description,
                        ];
                    });
                })
                ->toArray();
        });
    }

    /**
     * Get all settings in a specific group
     *
     * @param string $group
     * @return array
     */
    public function getGroup(string $group): array
    {
        return Cache::remember("settings_group_{$group}", self::CACHE_TTL, function () use ($group) {
            return SystemSetting::where('group', $group)
                ->get()
                ->map(function ($setting) {
                    return [
                        'key' => $setting->key,
                        'value' => $setting->casted_value,
                        'type' => $setting->type,
                        'label' => $setting->label,
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Clear all settings cache
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget('settings_all_grouped');

        // Clear individual setting caches
        SystemSetting::all()->each(function ($setting) {
            Cache::forget("setting_{$setting->key}");
            Cache::forget("settings_group_{$setting->group}");
        });
    }

    /**
     * Check if a setting exists
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return SystemSetting::where('key', $key)->exists();
    }

    /**
     * Delete a setting
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        $setting = SystemSetting::where('key', $key)->first();

        if ($setting) {
            Cache::forget("setting_{$key}");
            return $setting->delete();
        }

        return false;
    }
}
