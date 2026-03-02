<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General settings
            ['key' => 'app_name', 'value' => 'Warta Daerah Muna Barat', 'type' => 'string', 'group' => 'general'],
            ['key' => 'app_subtitle', 'value' => 'Pemerintah Kabupaten Muna Barat', 'type' => 'string', 'group' => 'general'],
            ['key' => 'app_description', 'value' => 'Portal informasi resmi yang menyajikan berita terkini, artikel berkualitas, dan liputan mendalam tentang perkembangan Kabupaten Muna Barat.', 'type' => 'text', 'group' => 'general'],
            ['key' => 'app_address', 'value' => 'Jl. Poros Kendari - Bau-Bau Km. 10, Sawerigadi, Muna Barat', 'type' => 'string', 'group' => 'general'],
            ['key' => 'app_email', 'value' => 'info@muna-barat.go.id', 'type' => 'string', 'group' => 'general'],
            ['key' => 'app_phone', 'value' => '(0402) 123456', 'type' => 'string', 'group' => 'general'],
            
            // Social media
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/munabarat', 'type' => 'string', 'group' => 'social'],
            ['key' => 'twitter_url', 'value' => 'https://twitter.com/munabarat', 'type' => 'string', 'group' => 'social'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/munabarat', 'type' => 'string', 'group' => 'social'],
            ['key' => 'youtube_url', 'value' => 'https://youtube.com/munabarat', 'type' => 'string', 'group' => 'social'],
            
            // Appearance
            ['key' => 'app_logo', 'value' => null, 'type' => 'image', 'group' => 'appearance'],
            ['key' => 'app_favicon', 'value' => null, 'type' => 'image', 'group' => 'appearance'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}