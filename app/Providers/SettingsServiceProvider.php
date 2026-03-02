<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Override app name with setting if available
        // Only attempt to load settings if the table exists (to avoid errors during migrate:fresh --seed)
        if (class_exists(\App\Models\Setting::class) 
            && Schema::hasTable('settings')) {
            try {
                $appName = \App\Models\Setting::get('app_name');
                if ($appName) {
                    Config::set('app.name', $appName);
                }
            } catch (\Exception $e) {
                // Silently fail if settings table is not accessible
                // This can happen during migrations or seeding
            }
        }
    }
}