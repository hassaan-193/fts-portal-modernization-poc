<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;


class GeoValidationService
{
    /**
     * Earth's radius in meters
     */
    const EARTH_RADIUS_METERS = 6371000;

    /**
     * Validate if employee is within office geo-fence
     *
     * @param float $employeeLatitude - Employee's current latitude
     * @param float $employeeLongitude - Employee's current longitude
     * 
     * @return array [
     *     'is_valid' => bool,
     *     'distance_meters' => float,
     *     'allowed_radius' => int,
     *     'message' => string
     * ]
     */
    public function validateLocation($employeeLatitude, $employeeLongitude)
    {
        try {
            // Get office location from .env
            $officeLatitude = (float) config('attendance.office_latitude');
            $officeLongitude = (float) config('attendance.office_longitude');
            $allowedRadius = (int) config('attendance.geofence_radius_meters');

            // Validate input coordinates
            if (!$this->isValidLatitude($employeeLatitude) || !$this->isValidLongitude($employeeLongitude)) {
                return [
                    'is_valid' => false,
                    'distance_meters' => 0,
                    'allowed_radius' => $allowedRadius,
                    'message' => 'Invalid employee coordinates provided',
                ];
            }

            // Calculate distance using Haversine formula
            $distance = $this->calculateHaversineDistance(
                $employeeLatitude,
                $employeeLongitude,
                $officeLatitude,
                $officeLongitude
            );

            // Check if within allowed radius
            $isValid = $distance <= $allowedRadius;

            return [
                'is_valid' => $isValid,
                'distance_meters' => round($distance, 2),
                'allowed_radius' => $allowedRadius,
                'message' => $isValid 
                    ? "Within geofence ({$distance}m from office)"
                    : "Outside geofence ({$distance}m from office, allowed: {$allowedRadius}m)",
            ];

        } catch (\Exception $e) {
            Log::error('Geo validation error: ' . $e->getMessage());
            return [
                'is_valid' => false,
                'distance_meters' => 0,
                'allowed_radius' => config('attendance.geofence_radius_meters'),
                'message' => 'Error validating location: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     *
     * @param float $lat1 - Latitude 1
     * @param float $lon1 - Longitude 1
     * @param float $lat2 - Latitude 2 (office)
     * @param float $lon2 - Longitude 2 (office)
     * 
     * @return float Distance in meters
     */
    public function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        // Convert degrees to radians
        $lat1_rad = deg2rad($lat1);
        $lon1_rad = deg2rad($lon1);
        $lat2_rad = deg2rad($lat2);
        $lon2_rad = deg2rad($lon2);

        // Differences
        $dlat = $lat2_rad - $lat1_rad;
        $dlon = $lon2_rad - $lon1_rad;

        // Haversine formula
        $a = sin($dlat / 2) ** 2 + cos($lat1_rad) * cos($lat2_rad) * sin($dlon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = self::EARTH_RADIUS_METERS * $c;

        return $distance;
    }

    /**
     * Validate latitude (-90 to 90)
     */
    private function isValidLatitude($latitude)
    {
        return is_numeric($latitude) && $latitude >= -90 && $latitude <= 90;
    }

    /**
     * Validate longitude (-180 to 180)
     */
    private function isValidLongitude($longitude)
    {
        return is_numeric($longitude) && $longitude >= -180 && $longitude <= 180;
    }

    /**
     * Get office location coordinates from .env
     */
    public function getOfficeCoordinates()
    {
        return [
            'latitude' => (float) config('attendance.office_latitude'),
            'longitude' => (float) config('attendance.office_longitude'),
            'radius_meters' => (int) config('attendance.geofence_radius_meters'),
            'name' => config('attendance.office_name'),
        ];
    }

    /**
     * Get formatted distance string
     */
    public function formatDistance($meters)
    {
        if ($meters >= 1000) {
            return round($meters / 1000, 2) . ' km';
        }
        return round($meters, 2) . ' m';
    }
}
