<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkProgram;
use Illuminate\Http\Request;

class WorkProgramController extends Controller
{
    /**
     * Display a listing of the work programs.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = WorkProgram::with('author');
        
        // Filter by category if provided
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Search by title or description if provided
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Order by priority and start date
        $query->orderBy('priority', 'desc')
              ->orderBy('start_date', 'asc');
        
        // Paginate results
        $perPage = $request->get('per_page', 10);
        $workPrograms = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $workPrograms->items(),
            'pagination' => [
                'current_page' => $workPrograms->currentPage(),
                'last_page' => $workPrograms->lastPage(),
                'per_page' => $workPrograms->perPage(),
                'total' => $workPrograms->total(),
            ]
        ]);
    }

    /**
     * Display the specified work program.
     *
     * @param  \App\Models\WorkProgram  $workProgram
     * @return \Illuminate\Http\Response
     */
    public function show(WorkProgram $workProgram)
    {
        $workProgram->load('author');
        
        return response()->json([
            'success' => true,
            'data' => $workProgram
        ]);
    }
}