<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\News;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Memastikan berita sudah ada
        if (News::count() == 0) {
            $this->call(NewsSeeder::class);
        }
        
        // Membuat 20 galeri dengan factory
        $galleries = Gallery::factory()->count(20)->create();
        
        // Menambahkan gambar ke setiap galeri
        foreach ($galleries as $gallery) {
            // Membuat 3-8 gambar untuk setiap galeri
            GalleryImage::factory()->count(rand(3, 8))->create([
                'gallery_id' => $gallery->id,
            ]);
        }
    }
}
