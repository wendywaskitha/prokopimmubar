<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Complaint::query();
        
        // Filter berdasarkan kategori jika ada
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        // Filter berdasarkan status jika ada
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan email jika ada
        if ($request->has('email')) {
            $query->where('email', $request->email);
        }
        
        // Search berdasarkan nama atau deskripsi
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Pagination
        $perPage = $request->get('per_page', 15);
        $complaints = $query->latest()->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $complaints->items(),
            'pagination' => [
                'current_page' => $complaints->currentPage(),
                'last_page' => $complaints->lastPage(),
                'per_page' => $complaints->perPage(),
                'total' => $complaints->total(),
            ]
        ]);
    }

    /**
     * Get complaint statistics
     */
    public function stats()
    {
        $countsByStatus = Complaint::getCountsByStatus();
        $totalCount = Complaint::getTotalCount();
        
        // Categorize counts for the frontend
        $stats = [
            'total' => $totalCount,
            'completed' => 0,
            'in_progress' => 0,
            'pending' => 0
        ];
        
        foreach ($countsByStatus as $status => $count) {
            switch($status) {
                case 'selesai':
                case 'ditolak':
                    $stats['completed'] += $count;
                    break;
                case 'diproses':
                    $stats['in_progress'] += $count;
                    break;
                case 'baru':
                    $stats['pending'] += $count;
                    break;
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $data = $request->only([
            'name', 'email', 'phone', 'category', 'description'
        ]);
        
        $data['status'] = 'baru';
        
        // Handle document upload
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('complaints/documents', 'public');
            $data['document'] = $documentPath;
        }
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('complaints/photos', 'public');
            $data['photo'] = $photoPath;
        }
        
        $complaint = Complaint::create($data);
        
        return response()->json([
            'success' => true,
            'message' => 'Aduan berhasil dikirim',
            'data' => $complaint
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $complaint = Complaint::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $complaint
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $complaint = Complaint::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:baru,diproses,selesai,ditolak',
            'response' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $data = $request->only(['status', 'response']);
        $complaint->update($data);
        
        return response()->json([
            'success' => true,
            'message' => 'Aduan berhasil diperbarui',
            'data' => $complaint
        ]);
    }
}