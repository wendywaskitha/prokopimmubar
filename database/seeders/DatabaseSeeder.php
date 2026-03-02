<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            NewsSeeder::class,
            GallerySeeder::class,
            ComplaintSeeder::class,
            SocialMediaSeeder::class,
            HeroSeeder::class,
            BannerSeeder::class,
            WorkProgramSeeder::class,
            AgendaSeeder::class,
            SettingsSeeder::class,
        ]);
    }
}
