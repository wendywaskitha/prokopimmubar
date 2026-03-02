<?php

use App\Repositories\SettingsRepository;

if (!function_exists('setting')) {
    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        static $settings = null;
        
        if ($settings === null) {
            $settingsRepository = app(SettingsRepository::class);
            $settings = $settingsRepository->all();
        }
        
        return $settings[$key] ?? $default;
    }
}