<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hero;
use Illuminate\Http\Request;

class HeroController extends Controller
{
    /**
     * Display a listing of active hero items.
     */
    public function index()
    {
        $heroes = Hero::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $heroes
        ]);
    }

    /**
     * Display the specified hero item.
     */
    public function show(string $id)
    {
        $hero = Hero::where('is_active', true)->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $hero
        ]);
    }
}