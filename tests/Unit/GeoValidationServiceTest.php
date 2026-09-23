<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\GeoValidationService;

/**
 * GeoValidationServiceTest — Full Branch Coverage
 *
 * Every if/else/catch in GeoValidationService is exercised below.
 *
 * SERVICE BRANCHES:
 *  validateLocation()
 *    ├── isValidLatitude: true + isValidLongitude: true  → happy path (✅ existing)
 *    ├── isValidLatitude: false (lat > 90)               → invalid response (✅ existing)
 *    ├── isValidLatitude: false (lat < -90)              → invalid response (🆕 NEW)
 *    ├── isValidLongitude: false (lon > 180)             → invalid response (✅ existing)
 *    ├── isValidLongitude: false (lon < -180)            → invalid response (🆕 NEW)
 *    ├── non-numeric latitude                            → invalid response (🆕 NEW)
 *    ├── within geofence ($distance <= $radius)          → is_valid=true  (✅ existing)
 *    └── outside geofence ($distance >  $radius)         → is_valid=false (✅ existing)
 *
 *  calculateHaversineDistance()
 *    ├── identical points → 0                           (✅ existing)
 *    └── known points in range                          (✅ existing)
 *
 *  getOfficeCoordinates()
 *    └── returns array with correct keys                (🆕 NEW)
 *
 *  formatDistance()
 *    ├── < 1000 m → meters string                       (✅ existing)
 *    ├── >= 1000 m → km string                          (✅ existing)
 *    └── exactly 1000 m → km boundary                  (🆕 NEW)
 */
class GeoValidationServiceTest extends TestCase
{
    protected $geoService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->geoService = new GeoValidationService();

        // Configure standard office location in Dubai for tests
        config([
            'attendance.office_latitude'         => 25.2048,
            'attendance.office_longitude'        => 55.2708,
            'attendance.geofence_radius_meters'  => 500,
            'attendance.office_name'             => 'FTS Head Office',
        ]);
    }

    // ------------------------------------------------------------------
    // calculateHaversineDistance — two confirmed paths
    // ------------------------------------------------------------------

    /**
     * Test Haversine distance calculation between identical coordinates is 0.
     */
    public function testHaversineDistanceIdenticalPoints()
    {
        $distance = $this->geoService->calculateHaversineDistance(25.2048, 55.2708, 25.2048, 55.2708);
        $this->assertEquals(0.0, round($distance, 2));
    }

    /**
     * Test Haversine distance calculation between known Dubai coordinates.
     * Burj Khalifa (25.1972, 55.2744) to Dubai Mall (25.1988, 55.2796) is roughly ~500-600m.
     */
    public function testHaversineDistanceKnownPoints()
    {
        $lat1 = 25.1972;
        $lon1 = 55.2744;
        $lat2 = 25.1988;
        $lon2 = 55.2796;

        $distance = $this->geoService->calculateHaversineDistance($lat1, $lon1, $lat2, $lon2);

        $this->assertGreaterThan(400, $distance);
        $this->assertLessThan(700, $distance);
    }

    // ------------------------------------------------------------------
    // validateLocation — happy paths (within / outside geofence)
    // ------------------------------------------------------------------

    /**
     * Test validation succeeds when employee is within the 500m geofence.
     * Branch: isValidLatitude=true, isValidLongitude=true, distance<=radius
     */
    public function testValidateLocationWithinGeofence()
    {
        // Very slight offset (< 100 meters away from office)
        $employeeLat = 25.2050;
        $employeeLon = 55.2710;

        $result = $this->geoService->validateLocation($employeeLat, $employeeLon);

        $this->assertTrue($result['is_valid']);
        $this->assertLessThanOrEqual(500, $result['distance_meters']);
        $this->assertStringContainsString('Within geofence', $result['message']);
    }

    /**
     * Test validation fails when employee is outside the allowed geofence radius.
     * Branch: isValidLatitude=true, isValidLongitude=true, distance>radius
     */
    public function testValidateLocationOutsideGeofence()
    {
        // ~5 km away from the office
        $employeeLat = 25.2500;
        $employeeLon = 55.3000;

        $result = $this->geoService->validateLocation($employeeLat, $employeeLon);

        $this->assertFalse($result['is_valid']);
        $this->assertGreaterThan(500, $result['distance_meters']);
        $this->assertStringContainsString('Outside geofence', $result['message']);
    }

    // ------------------------------------------------------------------
    // validateLocation — invalid coordinate branches
    // ------------------------------------------------------------------

    /**
     * Invalid latitude > 90 returns invalid response.
     * Branch: isValidLatitude=false (positive overflow)
     */
    public function testValidateLocationWithInvalidLatitudePositiveOverflow()
    {
        $result = $this->geoService->validateLocation(95.0, 55.2708);

        $this->assertFalse($result['is_valid']);
        $this->assertEquals(0, $result['distance_meters']);
        $this->assertStringContainsString('Invalid employee coordinates', $result['message']);
    }

    /**
     * 🆕 Invalid latitude < -90 returns invalid response.
     * Branch: isValidLatitude=false (negative overflow — previously untested)
     */
    public function testValidateLocationWithInvalidLatitudeNegativeOverflow()
    {
        $result = $this->geoService->validateLocation(-95.0, 55.2708);

        $this->assertFalse($result['is_valid']);
        $this->assertEquals(0, $result['distance_meters']);
        $this->assertStringContainsString('Invalid employee coordinates', $result['message']);
    }

    /**
     * 🆕 Invalid longitude < -180 returns invalid response.
     * Branch: isValidLongitude=false (negative overflow — previously untested)
     */
    public function testValidateLocationWithInvalidLongitudeNegativeOverflow()
    {
        $result = $this->geoService->validateLocation(25.2048, -195.0);

        $this->assertFalse($result['is_valid']);
        $this->assertEquals(0, $result['distance_meters']);
        $this->assertStringContainsString('Invalid employee coordinates', $result['message']);
    }

    /**
     * Invalid longitude > 180 returns invalid response.
     * Branch: isValidLongitude=false (positive overflow)
     */
    public function testValidateLocationWithInvalidLongitudePositiveOverflow()
    {
        $result = $this->geoService->validateLocation(25.2048, 195.0);

        $this->assertFalse($result['is_valid']);
        $this->assertStringContainsString('Invalid employee coordinates', $result['message']);
    }

    /**
     * 🆕 Non-numeric latitude returns invalid response.
     * Branch: is_numeric($latitude)=false
     */
    public function testValidateLocationWithNonNumericLatitude()
    {
        $result = $this->geoService->validateLocation('not-a-number', 55.2708);

        $this->assertFalse($result['is_valid']);
        $this->assertStringContainsString('Invalid employee coordinates', $result['message']);
    }

    /**
     * 🆕 Non-numeric longitude returns invalid response.
     * Branch: is_numeric($longitude)=false
     */
    public function testValidateLocationWithNonNumericLongitude()
    {
        $result = $this->geoService->validateLocation(25.2048, 'not-a-number');

        $this->assertFalse($result['is_valid']);
        $this->assertStringContainsString('Invalid employee coordinates', $result['message']);
    }

    // ------------------------------------------------------------------
    // getOfficeCoordinates — previously untested
    // ------------------------------------------------------------------

    /**
     * 🆕 getOfficeCoordinates returns the correct 4-key array from config.
     * Covers the entire getOfficeCoordinates() method body.
     */
    public function testGetOfficeCoordinatesReturnsCorrectShape()
    {
        $coords = $this->geoService->getOfficeCoordinates();

        $this->assertArrayHasKey('latitude', $coords);
        $this->assertArrayHasKey('longitude', $coords);
        $this->assertArrayHasKey('radius_meters', $coords);
        $this->assertArrayHasKey('name', $coords);

        $this->assertEquals(25.2048, $coords['latitude']);
        $this->assertEquals(55.2708, $coords['longitude']);
        $this->assertEquals(500, $coords['radius_meters']);
        $this->assertEquals('FTS Head Office', $coords['name']);
    }

    // ------------------------------------------------------------------
    // formatDistance — two existing + one boundary
    // ------------------------------------------------------------------

    /**
     * formatDistance branches: < 1000m → meters, >= 1000m → km, exact 1000m boundary.
     */
    public function testFormatDistance()
    {
        // < 1000 m → meters path
        $this->assertEquals('250 m',    $this->geoService->formatDistance(250));
        $this->assertEquals('450.5 m',  $this->geoService->formatDistance(450.5));

        // > 1000 m → km path
        $this->assertEquals('1.5 km',   $this->geoService->formatDistance(1500));
        $this->assertEquals('5.25 km',  $this->geoService->formatDistance(5250));

        // 🆕 Exact 1000 m boundary (>= 1000 → km path)
        $this->assertEquals('1 km', $this->geoService->formatDistance(1000));
    }

    /**
     * 🆕 Test catch block in validateLocation when calculation throws exception.
     */
    public function testValidateLocationCatchesException()
    {
        $mockService = new class extends GeoValidationService {
            public function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2)
            {
                throw new \Exception('GPS computation fault');
            }
        };

        $result = $mockService->validateLocation(25.2048, 55.2708);
        $this->assertFalse($result['is_valid']);
        $this->assertEquals(0, $result['distance_meters']);
        $this->assertStringContainsString('Error validating location: GPS computation fault', $result['message']);
    }
}
