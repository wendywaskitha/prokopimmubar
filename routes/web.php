<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ComplaintController;

// Route untuk aplikasi Vue.js
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');

// Routes untuk preview berita
Route::get('/news/preview/{id?}', [NewsController::class, 'preview'])->name('news.preview');

// Routes untuk aduan masyarakat API (handled by Vue.js frontend)
// Route::get('/complaint', [ComplaintController::class, 'create'])->name('complaint.create');
// Route::post('/complaint', [ComplaintController::class, 'store'])->name('complaint.store');
