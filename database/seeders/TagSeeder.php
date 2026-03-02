<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat tag berita umum
        $tags = [
            ['name' => 'penting', 'slug' => 'penting'],
            ['name' => 'terkini', 'slug' => 'terkini'],
            ['name' => 'lokal', 'slug' => 'lokal'],
            ['name' => 'nasional', 'slug' => 'nasional'],
            ['name' => 'internasional', 'slug' => 'internasional'],
            ['name' => 'opini', 'slug' => 'opini'],
            ['name' => 'analisis', 'slug' => 'analisis'],
            ['name' => 'wawancara', 'slug' => 'wawancara'],
            ['name' => 'liputan', 'slug' => 'liputan'],
            ['name' => 'khusus', 'slug' => 'khusus'],
        ];
        
        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
