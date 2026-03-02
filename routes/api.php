<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\SocialMediaController;
use App\Http\Controllers\Api\HeroController;
use App\Http\Controllers\Api\WorkProgramController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\AgendaController;
use App\Http\Controllers\Api\SettingsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('api')->prefix('v1')->group(function () {
    // Settings routes
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::get('/settings/{key}', [SettingsController::class, 'show']);
    
    // Hero routes
    Route::get('/heroes', [HeroController::class, 'index']);
    Route::get('/heroes/{id}', [HeroController::class, 'show']);
    
    // Gallery routes
    Route::get('/galleries', [GalleryController::class, 'index']);
    Route::get('/galleries/{id}', [GalleryController::class, 'show']);
    
    // Complaint routes
    Route::post('/complaints', [ComplaintController::class, 'store'])->middleware('complaint.rate.limit');
    Route::get('/complaints', [ComplaintController::class, 'index']);
    Route::get('/complaints/{id}', [ComplaintController::class, 'show']);
    Route::put('/complaints/{id}', [ComplaintController::class, 'update']);
    Route::get('/complaints-stats', [ComplaintController::class, 'stats']);
    
    // News routes
    Route::get('/news', [NewsController::class, 'index']);
    Route::get('/news/{id}', [NewsController::class, 'show']);
    Route::get('/news/category/{category}', [NewsController::class, 'byCategory']);
    Route::get('/news/popular', [NewsController::class, 'popular']);
    
    // Social Media routes
    Route::get('/social-media', [SocialMediaController::class, 'index']);
    Route::get('/social-media/{id}', [SocialMediaController::class, 'show']);
    Route::get('/social-media/platform/{platform}', [SocialMediaController::class, 'byPlatform']);
    
    // Work Program routes
    Route::get('/work-programs', [WorkProgramController::class, 'index']);
    Route::get('/work-programs/{workProgram}', [WorkProgramController::class, 'show']);
    
    // Category routes
    Route::get('/categories', [CategoryController::class, 'index']);
    
    // Banner routes
    Route::get('/banners', [BannerController::class, 'index']);
    Route::get('/banners/position/{position}', [BannerController::class, 'byPosition']);
    Route::get('/banners/opd-head-greetings', [BannerController::class, 'opdHeadGreetings']);
    Route::get('/banners/{banner}', [BannerController::class, 'show']);
    Route::post('/banners/{banner}/click', [BannerController::class, 'click']);
    
    // Agenda routes
    Route::get('/agendas', [AgendaController::class, 'index']);
    Route::get('/agendas/{agenda}', [AgendaController::class, 'show']);
    Route::get('/agendas-upcoming', [AgendaController::class, 'upcoming']);
    Route::get('/agendas-ongoing', [AgendaController::class, 'ongoing']);
});