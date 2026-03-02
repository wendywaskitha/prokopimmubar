<?php

namespace App\Repositories;

use App\Models\Setting;

class SettingsRepository
{
    /**
     * Get all settings as key-value pairs
     */
    public function all()
    {
        return Setting::pluck('value', 'key')->toArray();
    }

    /**
     * Get a setting value by key
     */
    public function get(string $key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key
     */
    public function set(string $key, $value, string $type = 'string', string $group = 'general')
    {
        return Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group
            ]
        );
    }

    /**
     * Get settings by group
     */
    public function getByGroup(string $group)
    {
        return Setting::where('group', $group)->pluck('value', 'key')->toArray();
    }
}