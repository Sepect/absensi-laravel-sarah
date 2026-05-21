<?php

namespace App\Services;

use App\Models\OfficeSetting;

class HaversineService
{
    /**
     * Earth's radius in meters
     */
    private const EARTH_RADIUS = 6371000;

    /**
     * Calculate distance between two coordinates using Haversine formula
     *
     * @param float $lat1 Latitude of point 1
     * @param float $lon1 Longitude of point 1
     * @param float $lat2 Latitude of point 2
     * @param float $lon2 Longitude of point 2
     * @return float Distance in meters
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
            cos($lat1Rad) * cos($lat2Rad) *
            sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS * $c;
    }

    /**
     * Check if given coordinates are within office radius
     *
     * @param float $userLat User's latitude
     * @param float $userLon User's longitude
     * @param OfficeSetting|null $office Office settings to check against (null = get from database)
     * @return array ['within_radius' => bool, 'distance' => float, 'max_radius' => int]
     */
    public function isWithinOfficeRadius(
        float $userLat,
        float $userLon,
        ?OfficeSetting $office = null
    ): array {
        $office = $office ?? OfficeSetting::getActive();

        $distance = $this->calculateDistance(
            $userLat,
            $userLon,
            (float) $office->latitude,
            (float) $office->longitude
        );

        return [
            'within_radius' => $distance <= $office->radius_meters,
            'distance' => round($distance, 2),
            'max_radius' => $office->radius_meters,
        ];
    }

    /**
     * Get formatted distance string
     */
    public function formatDistance(float $meters): string
    {
        if ($meters < 1000) {
            return round($meters) . 'm';
        }

        return number_format($meters / 1000, 2) . 'km';
    }
}