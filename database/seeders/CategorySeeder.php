<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat kategori berita utama
        $categories = [
            [
                'name' => 'Berita Umum',
                'slug' => 'berita-umum',
                'description' => 'Berita umum seputar kabupaten'
            ],
            [
                'name' => 'Pemerintahan',
                'slug' => 'pemerintahan',
                'description' => 'Berita tentang pemerintahan daerah'
            ],
            [
                'name' => 'Ekonomi',
                'slug' => 'ekonomi',
                'description' => 'Berita tentang perkembangan ekonomi daerah'
            ],
            [
                'name' => 'Pendidikan',
                'slug' => 'pendidikan',
                'description' => 'Berita tentang dunia pendidikan'
            ],
            [
                'name' => 'Kesehatan',
                'slug' => 'kesehatan',
                'description' => 'Berita tentang kesehatan masyarakat'
            ],
            [
                'name' => 'Olahraga',
                'slug' => 'olahraga',
                'description' => 'Berita tentang olahraga daerah'
            ],
            [
                'name' => 'Wisata',
                'slug' => 'wisata',
                'description' => 'Berita tentang tempat wisata daerah'
            ],
            [
                'name' => 'Budaya',
                'slug' => 'budaya',
                'description' => 'Berita tentang budaya lokal'
            ],
            [
                'name' => 'Pertanian',
                'slug' => 'pertanian',
                'description' => 'Berita tentang sektor pertanian'
            ],
            [
                'name' => 'Peternakan',
                'slug' => 'peternakan',
                'description' => 'Berita tentang sektor peternakan'
            ],
            [
                'name' => 'Perikanan',
                'slug' => 'perikanan',
                'description' => 'Berita tentang sektor perikanan'
            ],
            [
                'name' => 'Pariwisata',
                'slug' => 'pariwisata',
                'description' => 'Berita tentang pengembangan pariwisata'
            ],
        ];
        
        foreach ($categories as $categoryData) {
            // Cek apakah kategori sudah ada
            $category = Category::where('slug', $categoryData['slug'])->first();
            
            if ($category) {
                // Update kategori yang sudah ada
                $category->update($categoryData);
            } else {
                // Buat kategori baru jika belum ada
                Category::create($categoryData);
            }
        }
    }
}
