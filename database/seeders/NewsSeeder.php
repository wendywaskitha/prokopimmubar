<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Memastikan kategori dan tag sudah ada
        if (Category::count() == 0) {
            $this->call(CategorySeeder::class);
        }
        
        if (Tag::count() == 0) {
            $this->call(TagSeeder::class);
        }
        
        if (User::count() == 0) {
            $this->call(UserSeeder::class);
        }
        
        // Membuat 50 berita dengan factory
        $newsItems = News::factory()->count(50)->published()->create();
        
        // Menambahkan kategori dan tag ke setiap berita
        $categories = Category::all();
        $tags = Tag::all();
        $users = User::all();
        
        foreach ($newsItems as $news) {
            // Menambahkan kategori acak (1-3 kategori)
            $news->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')->toArray()
            );
            
            // Menambahkan tag acak (1-5 tag)
            $news->tags()->attach(
                $tags->random(rand(1, 5))->pluck('id')->toArray()
            );
            
            // Menentukan author acak
            $news->author_id = $users->random()->id;
            $news->save();
        }
    }
}
