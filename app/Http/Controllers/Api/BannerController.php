<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Display a listing of the active banners.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banners = Banner::active()
            ->withinDateRange()
            ->orderBy('position')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $banners
        ]);
    }

    /**
     * Display a listing of banners by position.
     *
     * @param  string  $position
     * @return \Illuminate\Http\Response
     */
    public function byPosition($position)
    {
        $banners = Banner::active()
            ->withinDateRange()
            ->where('position', $position)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $banners
        ]);
    }

    /**
     * Display a listing of OPD head greeting banners.
     *
     * @return \Illuminate\Http\Response
     */
    public function opdHeadGreetings()
    {
        $banners = Banner::active()
            ->withinDateRange()
            ->where('position', 'opd-head-greeting')
            ->where('size', 'card-1x1')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $banners
        ]);
    }

    /**
     * Display the specified banner.
     *
     * @param  \App\Models\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function show(Banner $banner)
    {
        if (!$banner->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Banner tidak ditemukan atau tidak aktif.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $banner
        ]);
    }

    /**
     * Increment the click count for a banner.
     *
     * @param  \App\Models\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function click(Banner $banner)
    {
        if (!$banner->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Banner tidak ditemukan atau tidak aktif.'
            ], 404);
        }

        $banner->increment('click_count');

        return response()->json([
            'success' => true,
            'message' => 'Click count updated successfully.'
        ]);
    }
}