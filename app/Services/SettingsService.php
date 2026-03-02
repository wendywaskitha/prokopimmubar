<?php

namespace App\Services;

use App\Models\Setting;

class SettingsService
{
    /**
     * Get a setting value by key
     */
    public function get(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }

    /**
     * Set a setting value by key
     */
    public function set(string $key, $value, string $type = 'string', string $group = 'general')
    {
        return Setting::set($key, $value, $type, $group);
    }

    /**
     * Get all settings as key-value pairs
     */
    public function all()
    {
        return Setting::pluck('value', 'key')->toArray();
    }

    /**
     * Get settings by group
     */
    public function getByGroup(string $group)
    {
        return Setting::where('group', $group)->pluck('value', 'key')->toArray();
    }
}