<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Banner;

class OpdHeadGreetingBannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample OPD head greeting banners
        Banner::factory()
            ->count(5)
            ->opdHeadGreeting()
            ->create();
    }
}
