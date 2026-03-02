<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = News::with(['author', 'categories', 'tags'])->where('status', 'published');
        
        // Log the request parameters
        \Log::info('News API request parameters:', $request->all());
        
        // Filter berdasarkan kategori jika ada
        if ($request->has('category')) {
            $categorySlug = $request->category;
            \Log::info('Filtering by category slug:', ['slug' => $categorySlug]);
            
            // Cek apakah kategori ada
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                \Log::info('Category found:', ['category' => $category]);
                $query->whereHas('categories', function($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            } else {
                \Log::warning('Category not found:', ['slug' => $categorySlug]);
            }
        }
        
        // Search berdasarkan judul atau konten
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        // Filter berdasarkan popularitas (dihitung dari created_at yang lebih awal)
        if ($request->has('popular')) {
            $query->orderBy('published_at', 'desc');
        }
        
        // Pagination
        $perPage = $request->get('per_page', 15);
        $news = $query->latest('published_at')->paginate($perPage);
        
        // Log the number of results
        \Log::info('Number of news items found:', ['count' => $news->count()]);
        
        return response()->json([
            'success' => true,
            'data' => $news->items(),
            'pagination' => [
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
                'per_page' => $news->perPage(),
                'total' => $news->total(),
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $news = News::with(['author', 'categories', 'tags', 'gallery.images'])
                    ->where('status', 'published')
                    ->where('slug', $id)
                    ->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    /**
     * Display news by category.
     */
    public function byCategory($categorySlug)
    {
        // Check if category exists
        $category = Category::where('slug', $categorySlug)->firstOrFail();
        
        $news = News::with(['author', 'categories', 'tags'])
                    ->where('status', 'published')
                    ->whereHas('categories', function($q) use ($categorySlug) {
                        $q->where('slug', $categorySlug);
                    })
                    ->latest('published_at')
                    ->paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $news->items(),
            'category' => $category,
            'pagination' => [
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
                'per_page' => $news->perPage(),
                'total' => $news->total(),
            ]
        ]);
    }

    /**
     * Display popular news.
     */
    public function popular()
    {
        $news = News::with(['author', 'categories', 'tags'])
                    ->where('status', 'published')
                    ->orderBy('published_at', 'desc')
                    ->limit(10)
                    ->get();
        
        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }
}