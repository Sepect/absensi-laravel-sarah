<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OfficeSetting;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    /**
     * Get office settings
     * GET /api/office-settings
     */
    public function index(): JsonResponse
    {
        $settings = OfficeSetting::getActive();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $settings->id,
                'nama_instansi' => $settings->nama_instansi,
                'latitude' => (float) $settings->latitude,
                'longitude' => (float) $settings->longitude,
                'radius_meters' => $settings->radius_meters,
                'waktu_masuk' => $settings->waktu_masuk,
                'waktu_pulang' => $settings->waktu_pulang,
                'qr_expiry_minutes' => $settings->qr_expiry_minutes,
            ],
        ]);
    }
}