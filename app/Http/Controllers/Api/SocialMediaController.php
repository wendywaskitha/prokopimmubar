<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SocialMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SocialMediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Caching untuk 30 menit
        $cacheKey = 'social_media_all_' . md5(serialize($request->all()));
        
        $socialMedia = Cache::remember($cacheKey, 30, function () use ($request) {
            $query = SocialMedia::query();
            
            // Filter berdasarkan platform jika ada
            if ($request->has('platform')) {
                $query->where('platform', $request->platform);
            }
            
            // Pagination
            $perPage = $request->get('per_page', 15);
            return $query->latest()->paginate($perPage);
        });
        
        return response()->json([
            'success' => true,
            'data' => $socialMedia->items(),
            'pagination' => [
                'current_page' => $socialMedia->currentPage(),
                'last_page' => $socialMedia->lastPage(),
                'per_page' => $socialMedia->perPage(),
                'total' => $socialMedia->total(),
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Caching untuk 30 menit
        $cacheKey = 'social_media_' . $id;
        
        $socialMedia = Cache::remember($cacheKey, 30, function () use ($id) {
            return SocialMedia::findOrFail($id);
        });
        
        return response()->json([
            'success' => true,
            'data' => $socialMedia
        ]);
    }

    /**
     * Display social media by platform.
     */
    public function byPlatform(Request $request, $platform)
    {
        // Caching untuk 30 menit
        $cacheKey = 'social_media_platform_' . $platform . '_' . md5(serialize($request->all()));
        
        $socialMedia = Cache::remember($cacheKey, 30, function () use ($request, $platform) {
            $query = SocialMedia::where('platform', $platform);
            
            // Pagination
            $perPage = $request->get('per_page', 15);
            return $query->latest()->paginate($perPage);
        });
        
        return response()->json([
            'success' => true,
            'data' => $socialMedia->items(),
            'pagination' => [
                'current_page' => $socialMedia->currentPage(),
                'last_page' => $socialMedia->lastPage(),
                'per_page' => $socialMedia->perPage(),
                'total' => $socialMedia->total(),
            ]
        ]);
    }
}