<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Gallery::with(['news.categories', 'images']);
        
        // Filter berdasarkan berita jika ada
        if ($request->has('news_id')) {
            $query->where('news_id', $request->news_id);
        }
        
        // Filter berdasarkan kategori jika ada
        if ($request->has('category')) {
            $category = $request->category;
            $query->whereHas('news.categories', function ($q) use ($category) {
                $q->where('slug', $category);
            });
        }
        
        // Search berdasarkan judul
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }
        
        // Pagination
        $perPage = $request->get('per_page', 15);
        $galleries = $query->latest()->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $galleries->items(),
            'pagination' => [
                'current_page' => $galleries->currentPage(),
                'last_page' => $galleries->lastPage(),
                'per_page' => $galleries->perPage(),
                'total' => $galleries->total(),
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $gallery = Gallery::with(['news.categories', 'images'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $gallery
        ]);
    }
}
