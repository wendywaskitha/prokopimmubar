<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat 10 banner dengan berbagai posisi
        $banners = [
            [
                'title' => 'Pendaftaran CPNS 2025',
                'image' => 'banners/banner1.jpg',
                'link' => '/cpns',
                'position' => 'top',
                'size' => '728x90',
                'is_active' => true,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'click_count' => 0,
            ],
            [
                'title' => 'Vaksinasi COVID-19',
                'image' => 'banners/banner2.jpg',
                'link' => '/vaccination',
                'position' => 'sidebar',
                'size' => '300x250',
                'is_active' => true,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'click_count' => 0,
            ],
            [
                'title' => 'Pembayaran Pajak Online',
                'image' => 'banners/banner3.jpg',
                'link' => '/tax-payment',
                'position' => 'bottom',
                'size' => '728x90',
                'is_active' => true,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'click_count' => 0,
            ],
            [
                'title' => 'Pemilihan Kepala Daerah',
                'image' => 'banners/banner4.jpg',
                'link' => '/election',
                'position' => 'top',
                'size' => '728x90',
                'is_active' => true,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'click_count' => 0,
            ],
            [
                'title' => 'Bantuan Sosial',
                'image' => 'banners/banner5.jpg',
                'link' => '/social-assistance',
                'position' => 'sidebar',
                'size' => '300x250',
                'is_active' => true,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'click_count' => 0,
            ],
        ];
        
        foreach ($banners as $banner) {
            Banner::create($banner);
        }
        
        // Membuat 5 banner tambahan dengan factory
        Banner::factory(5)->create();
    }
}
