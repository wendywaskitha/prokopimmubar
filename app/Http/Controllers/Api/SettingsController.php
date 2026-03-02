<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\SettingsRepository;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    protected $settingsRepository;

    public function __construct(SettingsRepository $settingsRepository)
    {
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * Get all settings
     *
     * @return JsonResponse
     */
    public function index()
    {
        $settings = $this->settingsRepository->all();
        
        // Filter only the settings we want to expose to frontend
        $allowedSettings = [
            'app_name',
            'app_description',
            'app_address',
            'app_email',
            'app_phone',
            'facebook_url',
            'twitter_url',
            'instagram_url',
            'youtube_url',
            'app_logo',
            'app_favicon'
        ];

        $filteredSettings = array_intersect_key($settings, array_flip($allowedSettings));

        return response()->json([
            'success' => true,
            'data' => $filteredSettings
        ]);
    }

    /**
     * Get specific setting by key
     *
     * @param string $key
     * @return JsonResponse
     */
    public function show($key)
    {
        $allowedSettings = [
            'app_name',
            'app_description',
            'app_address',
            'app_email',
            'app_phone',
            'facebook_url',
            'twitter_url',
            'instagram_url',
            'youtube_url',
            'app_logo',
            'app_favicon'
        ];

        if (!in_array($key, $allowedSettings)) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not allowed'
            ], 403);
        }

        $value = $this->settingsRepository->get($key);

        return response()->json([
            'success' => true,
            'data' => [
                $key => $value
            ]
        ]);
    }
}