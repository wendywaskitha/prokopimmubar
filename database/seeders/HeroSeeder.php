<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hero;

class HeroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat 5 hero carousel
        $heroes = [
            [
                'title' => 'Selamat Datang di Warta Daerah Muna Barat',
                'description' => 'Portal informasi resmi Pemerintah Kabupaten Muna Barat',
                'image' => 'heroes/hero1.jpg',
                'link' => '/welcome',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Program Kerja Unggulan',
                'description' => 'Temukan program-program kerja unggulan kepala daerah',
                'image' => 'heroes/hero2.jpg',
                'link' => '/work-programs',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Galeri Foto Kegiatan',
                'description' => 'Dokumentasi kegiatan pemerintah daerah',
                'image' => 'heroes/hero3.jpg',
                'link' => '/galleries',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Layanan Publik',
                'description' => 'Informasi layanan publik untuk masyarakat',
                'image' => 'heroes/hero4.jpg',
                'link' => '/services',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'title' => 'Aduan Masyarakat',
                'description' => 'Sampaikan aduan Anda kepada pemerintah daerah',
                'image' => 'heroes/hero5.jpg',
                'link' => '/complaint',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];
        
        foreach ($heroes as $hero) {
            Hero::create($hero);
        }
    }
}
