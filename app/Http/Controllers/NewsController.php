<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    public function preview($id = null)
    {
        // Jika ID tidak diberikan, buat berita dummy untuk preview
        if (!$id || $id == 0) {
            $news = new News();
            $news->title = 'Preview Berita';
            $news->content = 'Ini adalah preview konten berita. Konten aktual akan ditampilkan di sini.';
            $news->excerpt = 'Ringkasan preview berita';
            $news->published_at = now();
            $news->author_id = Auth::id();
        } else {
            // Jika ID diberikan, ambil berita dari database
            $news = News::find($id);

            if (!$news) {
                abort(404);
            }
        }

        // Untuk preview, kita akan menampilkan dalam format sederhana
        // Dalam implementasi sebenarnya, ini akan menggunakan view template
        return view('news.preview', compact('news'));
    }
}
