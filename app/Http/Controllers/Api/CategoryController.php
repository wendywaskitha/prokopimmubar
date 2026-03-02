<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::select('id', 'name', 'slug')->get();
        
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
