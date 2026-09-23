<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ShiftRuleService;
use Carbon\Carbon;

/**
 * ShiftRuleServiceTest — Full Branch Coverage
 *
 * Every if/else in ShiftRuleService is exercised below.
 *
 * METHODS COVERED:
 *  detectShift()          — Saturday valid/invalid, Shift1, FridayShift2, Shift2,
 *                           Shift1 exact boundary, Shift2 exact boundary,
 *                           Friday before Friday-window, outside-all-windows
 *  detectLate()           — Shift1 on-time/late, Shift2 weekday on-time/late,
 *                           Shift2 Friday on-time/late, Saturday (via flag),
 *                           Saturday on-time, Saturday late, unknown-shift fallback
 *  getShiftDetails()      — Saturday, shift_1, shift_2 weekday, shift_2 Friday, null
 *  getAllShifts()          — returns 4 entries
 *  detectAbsence()        — Saturday absent, Saturday not absent, Shift1 absent,
 *                           Shift1 not absent, Shift2 (always not absent)
 *  isSaturdayCheckoutTooEarly() — too early, valid checkout
 */
class ShiftRuleServiceTest extends TestCase
{
    protected $shiftService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shiftService = new ShiftRuleService();
    }

    // ==================================================================
    // detectShift() — all branches
    // ==================================================================

    /**
     * Shift 1 detection on standard weekday (Monday 07:30 AM).
     */
    public function testDetectShift1Weekday()
    {
        $time = Carbon::parse('2026-09-14 07:30:00'); // Monday
        $result = $this->shiftService->detectShift($time);

        $this->assertTrue($result['is_valid']);
        $this->assertEquals('shift_1', $result['shift']);
        $this->assertFalse($result['is_saturday']);
    }

    /**
     * Shift 2 detection on standard weekday (Monday 14:15 PM).
     */
    public function testDetectShift2Weekday()
    {
        $time = Carbon::parse('2026-09-14 14:15:00'); // Monday
        $result = $this->shiftService->detectShift($time);

        $this->assertTrue($result['is_valid']);
        $this->assertEquals('shift_2', $result['shift']);
        $this->assertFalse($result['is_saturday']);
    }

    /**
     * Friday Shift 2 override (starts earlier at 1:30 PM).
     * Also confirms that Monday at 13:45 falls in the dead-zone (outside both windows).
     */
    public function testDetectFridayShift2Override()
    {
        // Friday 13:45 → Friday Shift 2 window (1:30–4:00 PM)
        $fridayEarly = Carbon::parse('2026-09-18 13:45:00');
        $result = $this->shiftService->detectShift($fridayEarly);

        $this->assertTrue($result['is_valid']);
        $this->assertEquals('shift_2', $result['shift']);
        $this->assertStringContainsString('Friday', $result['shift_name']);

        // Monday 13:45 → dead-zone (Shift 1 ends at 13:00, Shift 2 starts at 14:00)
        $mondayTime = Carbon::parse('2026-09-14 13:45:00');
        $mondayResult = $this->shiftService->detectShift($mondayTime);
        $this->assertFalse($mondayResult['is_valid']);
    }

    /**
     * Saturday single-shift schedule (9:00 AM – 1:00 PM).
     */
    public function testDetectSaturdayShift()
    {
        // Saturday 09:30 — within window
        $saturdayValid = Carbon::parse('2026-09-19 09:30:00');
        $result = $this->shiftService->detectShift($saturdayValid);

        $this->assertTrue($result['is_valid']);
        $this->assertEquals('shift_1', $result['shift']);
        $this->assertTrue($result['is_saturday']);

        // Saturday 07:30 — outside Saturday window (before 9:00 AM)
        $saturdayEarly = Carbon::parse('2026-09-19 07:30:00');
        $earlyResult = $this->shiftService->detectShift($saturdayEarly);
        $this->assertFalse($earlyResult['is_valid']);
    }

    /**
     * 🆕 Saturday after 13:00 (after Saturday end) — outside Saturday window.
     * Branch: Saturday path, isWithinSaturdayWindow=false (hour > SATURDAY_END_HOUR)
     */
    public function testDetectSaturdayAfterShiftEnd()
    {
        $saturdayLate = Carbon::parse('2026-09-19 14:00:00'); // Saturday 2 PM
        $result = $this->shiftService->detectShift($saturdayLate);

        $this->assertFalse($result['is_valid']);
        $this->assertNull($result['shift']);
        $this->assertTrue($result['is_saturday']);
    }

    /**
     * 🆕 Shift 1 exact boundary: exactly 13:00:00 is OUTSIDE Shift 1.
     * Branch: isWithinShift1Window — boundary check (hour===13 && minute>=0)
     */
    public function testDetectShift1ExactBoundaryIsOutside()
    {
        $exactly13 = Carbon::parse('2026-09-14 13:00:00'); // Monday exactly 1 PM
        $result = $this->shiftService->detectShift($exactly13);

        $this->assertFalse($result['is_valid'],
            '13:00:00 must fall OUTSIDE Shift 1 (Shift 1 is 7:00 to 12:59).');
    }

    /**
     * 🆕 Shift 2 exact boundary: exactly 16:00:00 is OUTSIDE Shift 2.
     * Branch: isWithinShift2Window — boundary check (hour===16 && minute>=0)
     */
    public function testDetectShift2ExactBoundaryIsOutside()
    {
        $exactly16 = Carbon::parse('2026-09-14 16:00:00'); // Monday exactly 4 PM
        $result = $this->shiftService->detectShift($exactly16);

        $this->assertFalse($result['is_valid'],
            '16:00:00 must fall OUTSIDE Shift 2 (Shift 2 is 14:00 to 15:59).');
    }

    /**
     * 🆕 Friday at 13:00 is outside both windows.
     * Branch: Friday, but hour=13:00 is before Friday Shift 2 window (13:30).
     */
    public function testDetectFridayBeforeFridayWindow()
    {
        $friday13 = Carbon::parse('2026-09-18 13:00:00'); // Friday 1 PM
        $result = $this->shiftService->detectShift($friday13);

        $this->assertFalse($result['is_valid']);
        $this->assertNull($result['shift']);
    }

    /**
     * Clock-in outside any shift window returns invalid.
     */
    public function testDetectShiftOutsideWindows()
    {
        // 05:00 AM — too early
        $earlyMorning = Carbon::parse('2026-09-14 05:00:00');
        $result = $this->shiftService->detectShift($earlyMorning);
        $this->assertFalse($result['is_valid']);
        $this->assertNull($result['shift']);

        // 22:00 PM — too late
        $lateNight = Carbon::parse('2026-09-14 22:00:00');
        $resultLate = $this->shiftService->detectShift($lateNight);
        $this->assertFalse($resultLate['is_valid']);
    }

    // ==================================================================
    // detectLate() — all branches
    // ==================================================================

    /**
     * Late detection for Shift 1 (on-time ≤ 08:00; late > 08:00).
     */
    public function testDetectLateShift1()
    {
        $onTime = Carbon::parse('2026-09-14 07:55:00');
        $resultOnTime = $this->shiftService->detectLate($onTime, 'shift_1');
        $this->assertFalse($resultOnTime['is_late']);

        $late = Carbon::parse('2026-09-14 08:05:00');
        $resultLate = $this->shiftService->detectLate($late, 'shift_1');
        $this->assertTrue($resultLate['is_late']);
    }

    /**
     * Late detection for Shift 2 (weekday late after 14:45; Friday late after 14:00).
     */
    public function testDetectLateShift2WeekdayAndFriday()
    {
        // Weekday on-time (Monday 14:30)
        $weekdayOnTime = Carbon::parse('2026-09-14 14:30:00');
        $res1 = $this->shiftService->detectLate($weekdayOnTime, 'shift_2');
        $this->assertFalse($res1['is_late']);

        // Weekday late (Monday 14:50)
        $weekdayLate = Carbon::parse('2026-09-14 14:50:00');
        $res2 = $this->shiftService->detectLate($weekdayLate, 'shift_2');
        $this->assertTrue($res2['is_late']);

        // Friday on-time (13:50)
        $fridayOnTime = Carbon::parse('2026-09-18 13:50:00');
        $res3 = $this->shiftService->detectLate($fridayOnTime, 'shift_2');
        $this->assertFalse($res3['is_late']);

        // Friday late (14:05)
        $fridayLate = Carbon::parse('2026-09-18 14:05:00');
        $res4 = $this->shiftService->detectLate($fridayLate, 'shift_2');
        $this->assertTrue($res4['is_late']);
    }

    /**
     * 🆕 Saturday late detection via $isSaturday=true flag (not relying on Carbon day).
     * Branch: if ($isSaturday || $clockInTime->isSaturday())
     */
    public function testDetectLateSaturdayViaFlag()
    {
        // Any Monday Carbon but passed $isSaturday=true — the flag forces the Saturday branch
        $mondayAt8 = Carbon::parse('2026-09-14 08:00:00');

        $result = $this->shiftService->detectLate($mondayAt8, 'shift_1', true);

        // 08:00 is before 09:00 Saturday deadline, so on-time
        $this->assertFalse($result['is_late']);
        $this->assertStringContainsString('Saturday', $result['message']);
    }

    /**
     * 🆕 Saturday on-time (before 09:00).
     * Branch: Saturday, $isLate=false (hour < SATURDAY_LATE_HOUR)
     */
    public function testDetectLateSaturdayOnTime()
    {
        $saturdayOnTime = Carbon::parse('2026-09-19 08:50:00'); // Saturday
        $result = $this->shiftService->detectLate($saturdayOnTime, 'shift_1');

        $this->assertFalse($result['is_late']);
        $this->assertStringContainsString('ON-TIME', $result['message']);
    }

    /**
     * 🆕 Saturday late (after 09:00).
     * Branch: Saturday, $isLate=true
     */
    public function testDetectLateSaturdayLate()
    {
        $saturdayLate = Carbon::parse('2026-09-19 09:15:00'); // Saturday
        $result = $this->shiftService->detectLate($saturdayLate, 'shift_1');

        $this->assertTrue($result['is_late']);
        $this->assertStringContainsString('LATE', $result['message']);
    }

    /**
     * 🆕 Unknown shift string falls through to the final return.
     * Branch: detectLate fallback — shift does not match 'shift_1' or 'shift_2'
     */
    public function testDetectLateUnknownShiftFallback()
    {
        $time = Carbon::parse('2026-09-14 10:00:00');
        $result = $this->shiftService->detectLate($time, 'shift_99');

        $this->assertFalse($result['is_late']);
        $this->assertNull($result['on_time_deadline']);
        $this->assertStringContainsString('invalid shift', $result['message']);
    }

    // ==================================================================
    // isSaturdayCheckoutTooEarly() — already covered, kept for completeness
    // ==================================================================

    /**
     * Saturday checkout too early rule (before 12:30 PM).
     */
    public function testSaturdayCheckoutTooEarly()
    {
        $early = Carbon::parse('2026-09-19 12:15:00');
        $this->assertTrue($this->shiftService->isSaturdayCheckoutTooEarly($early));

        $validCheckout = Carbon::parse('2026-09-19 12:45:00');
        $this->assertFalse($this->shiftService->isSaturdayCheckoutTooEarly($validCheckout));
    }

    /**
     * 🆕 Saturday checkout at exactly 12:30 — NOT too early (on the boundary).
     * Branch: hour===SATURDAY_CHECKOUT_HOUR && minute < SATURDAY_CHECKOUT_MINUTE → false
     */
    public function testSaturdayCheckoutExactlyOnTime()
    {
        $exactBoundary = Carbon::parse('2026-09-19 12:30:00');
        $this->assertFalse($this->shiftService->isSaturdayCheckoutTooEarly($exactBoundary));
    }

    // ==================================================================
    // getShiftDetails() — all 5 branches (Saturday, shift_1, shift_2, shift_2 Friday, null)
    // ==================================================================

    /**
     * 🆕 getShiftDetails — Saturday branch ($isSaturday=true).
     */
    public function testGetShiftDetailsSaturday()
    {
        $details = $this->shiftService->getShiftDetails('shift_1', true);

        $this->assertIsArray($details);
        $this->assertEquals('Saturday', $details['name']);
        $this->assertEquals('09:00', $details['checkin_start']);
        $this->assertEquals('09:00', $details['late_deadline']);
    }

    /**
     * 🆕 getShiftDetails — shift_1 weekday branch.
     */
    public function testGetShiftDetailsShift1()
    {
        $details = $this->shiftService->getShiftDetails('shift_1');

        $this->assertIsArray($details);
        $this->assertEquals('Shift 1', $details['name']);
        $this->assertEquals('07:00', $details['checkin_start']);
        $this->assertEquals('08:01', $details['late_deadline']);
    }

    /**
     * 🆕 getShiftDetails — shift_2 weekday branch.
     */
    public function testGetShiftDetailsShift2Weekday()
    {
        $details = $this->shiftService->getShiftDetails('shift_2');

        $this->assertIsArray($details);
        $this->assertEquals('Shift 2', $details['name']);
        $this->assertEquals('14:00', $details['checkin_start']);
        $this->assertEquals('14:45', $details['late_deadline']);
    }

    /**
     * 🆕 getShiftDetails — shift_2 Friday branch ($isFriday=true).
     */
    public function testGetShiftDetailsShift2Friday()
    {
        $details = $this->shiftService->getShiftDetails('shift_2', false, true);

        $this->assertIsArray($details);
        $this->assertEquals('Shift 2 (Friday)', $details['name']);
        $this->assertEquals('13:30', $details['checkin_start']);
        $this->assertEquals('14:00', $details['late_deadline']);
    }

    /**
     * 🆕 getShiftDetails — unknown shift returns null (fallback branch).
     */
    public function testGetShiftDetailsUnknownReturnsNull()
    {
        $details = $this->shiftService->getShiftDetails('shift_unknown');

        $this->assertNull($details);
    }

    // ==================================================================
    // getAllShifts() — previously untested
    // ==================================================================

    /**
     * 🆕 getAllShifts returns 4 configured shift entries.
     */
    public function testGetAllShiftsReturnsFourEntries()
    {
        $shifts = $this->shiftService->getAllShifts();

        $this->assertIsArray($shifts);
        $this->assertCount(4, $shifts);

        // Verify each entry has required keys
        foreach ($shifts as $shift) {
            $this->assertArrayHasKey('shift', $shift);
            $this->assertArrayHasKey('name', $shift);
            $this->assertArrayHasKey('checkin_start', $shift);
        }
    }

    // ==================================================================
    // detectAbsence() — all branches
    // ==================================================================

    /**
     * 🆕 Saturday absent — after 09:30 with no check-in.
     * Branch: isSaturday=true, $isAbsent=true
     */
    public function testDetectAbsenceSaturdayAbsent()
    {
        $saturday935 = Carbon::parse('2026-09-19 09:35:00');
        $result = $this->shiftService->detectAbsence($saturday935, 'shift_1');

        $this->assertTrue($result['is_absent']);
        $this->assertStringContainsString('ABSENT', $result['reason']);
    }

    /**
     * 🆕 Saturday NOT absent — before 09:30.
     * Branch: isSaturday=true, $isAbsent=false
     */
    public function testDetectAbsenceSaturdayNotAbsent()
    {
        $saturday9 = Carbon::parse('2026-09-19 09:00:00');
        $result = $this->shiftService->detectAbsence($saturday9, 'shift_1');

        $this->assertFalse($result['is_absent']);
        $this->assertNull($result['reason']);
    }

    /**
     * 🆕 Shift 1 absent — after 08:30 on a weekday.
     * Branch: shift_1, $isAbsent=true
     */
    public function testDetectAbsenceShift1Absent()
    {
        $weekday835 = Carbon::parse('2026-09-14 08:35:00'); // Monday 8:35 AM
        $result = $this->shiftService->detectAbsence($weekday835, 'shift_1');

        $this->assertTrue($result['is_absent']);
        $this->assertStringContainsString('8:30 AM', $result['reason']);
    }

    /**
     * 🆕 Shift 1 NOT absent — before 08:30 on a weekday.
     * Branch: shift_1, $isAbsent=false
     */
    public function testDetectAbsenceShift1NotAbsent()
    {
        $weekday8 = Carbon::parse('2026-09-14 08:00:00'); // Monday 8:00 AM
        $result = $this->shiftService->detectAbsence($weekday8, 'shift_1');

        $this->assertFalse($result['is_absent']);
        $this->assertNull($result['reason']);
    }

    /**
     * 🆕 Shift 2 always returns not-absent (no absence rule for Shift 2).
     * Branch: falls through to final return ['is_absent'=>false]
     */
    public function testDetectAbsenceShift2AlwaysNotAbsent()
    {
        // Even at 20:00 on a weekday, Shift 2 has no absence cutoff
        $weekday20 = Carbon::parse('2026-09-14 20:00:00');
        $result = $this->shiftService->detectAbsence($weekday20, 'shift_2');

        $this->assertFalse($result['is_absent']);
        $this->assertNull($result['reason']);
    }
}
