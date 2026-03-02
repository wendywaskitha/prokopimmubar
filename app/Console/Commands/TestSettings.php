<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\SettingsRepository;

class TestSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test settings functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $settingsRepository = app(SettingsRepository::class);
        
        // Test setting a value
        $settingsRepository->set('test_key', 'test_value', 'string', 'test');
        
        // Test getting a value
        $value = $settingsRepository->get('test_key', 'default_value');
        
        $this->info("Retrieved value: " . $value);
        
        // Test getting all settings
        $allSettings = $settingsRepository->all();
        $this->info("Total settings: " . count($allSettings));
        
        // Test helper function
        $helperValue = setting('test_key', 'default_helper_value');
        $this->info("Helper function value: " . $helperValue);
        
        $this->info("Settings functionality test completed!");
    }
}
