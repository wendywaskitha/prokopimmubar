<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    /**
     * Display a listing of the published agendas.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Agenda::where('is_published', true)
            ->with('creator');
            
        // Filter berdasarkan tanggal mulai jika ada
        if ($request->has('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        
        // Filter berdasarkan tanggal selesai jika ada
        if ($request->has('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }
        
        // Search berdasarkan judul atau lokasi
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        // Pagination
        $perPage = $request->get('per_page', 15);
        $agendas = $query->orderBy('start_date', 'asc')->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $agendas->items(),
            'pagination' => [
                'current_page' => $agendas->currentPage(),
                'last_page' => $agendas->lastPage(),
                'per_page' => $agendas->perPage(),
                'total' => $agendas->total(),
            ]
        ]);
    }

    /**
     * Display upcoming agendas.
     *
     * @return \Illuminate\Http\Response
     */
    public function upcoming()
    {
        $agendas = Agenda::where('is_published', true)
            ->where('start_date', '>=', now())
            ->with('creator')
            ->orderBy('start_date', 'asc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $agendas
        ]);
    }

    /**
     * Display ongoing agendas.
     *
     * @return \Illuminate\Http\Response
     */
    public function ongoing()
    {
        $agendas = Agenda::where('is_published', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with('creator')
            ->orderBy('start_date', 'asc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $agendas
        ]);
    }

    /**
     * Display the specified agenda.
     *
     * @param  \App\Models\Agenda  $agenda
     * @return \Illuminate\Http\Response
     */
    public function show(Agenda $agenda)
    {
        if (!$agenda->is_published) {
            return response()->json([
                'success' => false,
                'message' => 'Agenda tidak ditemukan atau tidak dipublikasikan.'
            ], 404);
        }

        $agenda->load('creator');

        return response()->json([
            'success' => true,
            'data' => $agenda
        ]);
    }
}