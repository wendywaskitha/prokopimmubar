<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    public function create()
    {
        return view('news.complaint-form');
    }
    
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
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
        
        return redirect()->back()->with('success', 'Aduan Anda telah berhasil dikirim. Kami akan segera memprosesnya.');
    }
    
    public function stats()
    {
        $total = Complaint::count();
        $completed = Complaint::where('status', 'selesai')->count();
        $inProgress = Complaint::whereIn('status', ['diproses'])->count();
        $pending = Complaint::where('status', 'baru')->count();
        
        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'completed' => $completed,
                'inProgress' => $inProgress,
                'pending' => $pending
            ]
        ]);
    }
}
