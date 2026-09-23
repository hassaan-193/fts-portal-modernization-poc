<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class ShiftRuleService
{
    // ============================================
    // SHIFT 1 CONFIGURATION
    // ============================================
    const SHIFT_1_START_HOUR = 7;
    const SHIFT_1_START_MINUTE = 0;        // 7:00 AM
    const SHIFT_1_END_HOUR = 13;
    const SHIFT_1_END_MINUTE = 0;          // 1:00 PM
    const SHIFT_1_CHECKOUT_START_HOUR = 13;
    const SHIFT_1_CHECKOUT_START_MINUTE = 0;  // 1:00 PM
    const SHIFT_1_CHECKOUT_END_HOUR = 14;
    const SHIFT_1_CHECKOUT_END_MINUTE = 0;    // 2:00 PM
    const SHIFT_1_LATE_HOUR = 8;
    const SHIFT_1_LATE_MINUTE = 0;            // On-time through 8:00; late from 8:01 AM (minute > 0 at hour 8)
    const SHIFT_1_ABSENT_HOUR = 8;
    const SHIFT_1_ABSENT_MINUTE = 30;         // 8:30 AM (considered absent after this)

    // ============================================
    // SHIFT 2 CONFIGURATION (Regular weekdays)
    // ============================================
    const SHIFT_2_START_HOUR = 14;         // 2:00 PM
    const SHIFT_2_START_MINUTE = 0;
    const SHIFT_2_END_HOUR = 16;           // 4:00 PM (check-in end time)
    const SHIFT_2_END_MINUTE = 0;
    const SHIFT_2_CHECKOUT_END_HOUR = 20;  // 8:00 PM (checkout goes until 8:00 PM)
    const SHIFT_2_CHECKOUT_END_MINUTE = 0;
    const SHIFT_2_LATE_HOUR = 14;
    const SHIFT_2_LATE_MINUTE = 45;            // 2:45 PM (considered late after this)

    // ============================================
    // FRIDAY SHIFT 2 OVERRIDES
    // Friday check-in starts at 1:30 PM, late after 2:00 PM
    // ============================================
    const FRIDAY_SHIFT_2_START_HOUR = 13;
    const FRIDAY_SHIFT_2_START_MINUTE = 30;    // 1:30 PM
    const FRIDAY_SHIFT_2_LATE_HOUR = 14;
    const FRIDAY_SHIFT_2_LATE_MINUTE = 0;      // 2:00 PM (considered late after this)

    // ============================================
    // SATURDAY CONFIGURATION
    // Working hours: 9:00 AM – 1:00 PM
    // Late after:    9:00 AM (9:01 AM+ is late)
    // Checkout:      allowed after 12:30 PM
    // ============================================
    const SATURDAY_START_HOUR = 9;
    const SATURDAY_START_MINUTE = 0;
    const SATURDAY_END_HOUR = 13;
    const SATURDAY_END_MINUTE = 0;
    const SATURDAY_LATE_HOUR = 9;
    const SATURDAY_LATE_MINUTE = 0;
    const SATURDAY_CHECKOUT_HOUR = 12;
    const SATURDAY_CHECKOUT_MINUTE = 30;
    const SATURDAY_ABSENT_HOUR = 9;
    const SATURDAY_ABSENT_MINUTE = 30;

    /**
     * Detect which shift a clock-in time belongs to
     *
     * @param Carbon $clockInTime - The clock-in timestamp
     * 
     * @return array [
     *     'shift' => 'shift_1' | 'shift_2',
     *     'shift_name' => 'Shift 1' | 'Shift 2',
     *     'is_valid' => bool (within shift window),
     *     'message' => string
     * ]
     */
    public function detectShift($clockInTime)
    {
        $hour = $clockInTime->hour;
        $minute = $clockInTime->minute;

        // Saturday has a single-shift schedule (9:00 AM – 1:00 PM), mapped to shift_1
        if ($clockInTime->isSaturday()) {
            if ($this->isWithinSaturdayWindow($hour, $minute)) {
                return [
                    'shift' => 'shift_1',
                    'shift_name' => 'Saturday (9:00 AM - 1:00 PM)',
                    'is_valid' => true,
                    'is_saturday' => true,
                    'message' => 'Clock-in detected for Saturday shift',
                ];
            }

            return [
                'shift' => null,
                'shift_name' => null,
                'is_valid' => false,
                'is_saturday' => true,
                'message' => 'Clock-in time is outside Saturday shift window (9:00 AM - 1:00 PM)',
            ];
        }

        // Check if clock-in is within Shift 1 window
        if ($this->isWithinShift1Window($hour, $minute)) {
            return [
                'shift' => 'shift_1',
                'shift_name' => 'Shift 1 (7:00 AM - 1:00 PM)',
                'is_valid' => true,
                'is_saturday' => false,
                'message' => 'Clock-in detected for Shift 1',
            ];
        }

        // Friday: Shift 2 check-in starts earlier at 1:30 PM
        if ($clockInTime->isFriday() && $this->isWithinFridayShift2Window($hour, $minute)) {
            return [
                'shift' => 'shift_2',
                'shift_name' => 'Shift 2 (1:30 PM - 4:00 PM, Friday)',
                'is_valid' => true,
                'is_saturday' => false,
                'message' => 'Clock-in detected for Shift 2 (Friday)',
            ];
        }

        // Regular weekdays: Shift 2 check-in from 2:00 PM
        if (!$clockInTime->isFriday() && $this->isWithinShift2Window($hour, $minute)) {
            return [
                'shift' => 'shift_2',
                'shift_name' => 'Shift 2 (2:00 PM - 4:00 PM)',
                'is_valid' => true,
                'is_saturday' => false,
                'message' => 'Clock-in detected for Shift 2',
            ];
        }

        // Outside both shift windows
        return [
            'shift' => null,
            'shift_name' => null,
            'is_valid' => false,
            'is_saturday' => false,
            'message' => 'Clock-in time is outside defined shift windows',
        ];
    }

    /**
     * Detect if clock-in is late based on shift window
     *
     * @param Carbon $clockInTime - The clock-in timestamp
     * @param string $shift - 'shift_1' or 'shift_2'
     * 
     * @return array [
     *     'is_late' => bool,
     *     'on_time_deadline' => Carbon,
     *     'message' => string
     * ]
     */
    public function detectLate($clockInTime, $shift, $isSaturday = false)
    {
        $hour = $clockInTime->hour;
        $minute = $clockInTime->minute;

        // Saturday uses its own grace period (9:00 AM)
        if ($isSaturday || $clockInTime->isSaturday()) {
            $lateDeadline = $clockInTime->clone()
                ->setHour(self::SATURDAY_LATE_HOUR)
                ->setMinute(self::SATURDAY_LATE_MINUTE);

            $isLate = $hour > self::SATURDAY_LATE_HOUR ||
                      ($hour === self::SATURDAY_LATE_HOUR && $minute > self::SATURDAY_LATE_MINUTE);

            return [
                'is_late' => $isLate,
                'on_time_deadline' => $lateDeadline,
                'message' => $isLate
                    ? "Clock-in at {$clockInTime->format('H:i')} is LATE (Saturday deadline: 9:00 AM)"
                    : "Clock-in at {$clockInTime->format('H:i')} is ON-TIME (Saturday)",
            ];
        }

        if ($shift === 'shift_1') {
            $lateDeadline = $clockInTime->clone()
                ->setHour(self::SHIFT_1_LATE_HOUR)
                ->setMinute(self::SHIFT_1_LATE_MINUTE);

            $isLate = $hour > self::SHIFT_1_LATE_HOUR ||
                      ($hour === self::SHIFT_1_LATE_HOUR && $minute > self::SHIFT_1_LATE_MINUTE);

            return [
                'is_late' => $isLate,
                'on_time_deadline' => $lateDeadline,
                'message' => $isLate
                    ? "Clock-in at {$clockInTime->format('H:i')} is LATE (late from 8:01 AM)"
                    : "Clock-in at {$clockInTime->format('H:i')} is ON-TIME",
            ];
        }

        if ($shift === 'shift_2') {
            $isFriday = $clockInTime->isFriday();
            $lateHour = $isFriday ? self::FRIDAY_SHIFT_2_LATE_HOUR : self::SHIFT_2_LATE_HOUR;
            $lateMinute = $isFriday ? self::FRIDAY_SHIFT_2_LATE_MINUTE : self::SHIFT_2_LATE_MINUTE;
            $deadlineLabel = $isFriday ? '2:00 PM' : '2:45 PM';

            $lateDeadline = $clockInTime->clone()
                ->setHour($lateHour)
                ->setMinute($lateMinute);

            $isLate = $hour > $lateHour ||
                      ($hour === $lateHour && $minute > $lateMinute);

            return [
                'is_late' => $isLate,
                'on_time_deadline' => $lateDeadline,
                'message' => $isLate
                    ? "Clock-in at {$clockInTime->format('H:i')} is LATE (on-time deadline: {$deadlineLabel})"
                    : "Clock-in at {$clockInTime->format('H:i')} is ON-TIME",
            ];
        }

        return [
            'is_late' => false,
            'on_time_deadline' => null,
            'message' => 'Cannot determine late status for invalid shift',
        ];
    }

    /**
     * Get shift details by shift identifier
     */
    public function getShiftDetails($shift, $isSaturday = false, $isFriday = false)
    {
        if ($isSaturday) {
            return [
                'shift' => 'shift_1',
                'name' => 'Saturday',
                'checkin_start' => '09:00',
                'checkin_end' => '13:00',
                'checkout_start' => '12:30',
                'checkout_end' => '13:00',
                'late_deadline' => '09:00',
                'absent_deadline' => '09:30',
            ];
        }

        if ($shift === 'shift_1') {
            return [
                'shift' => 'shift_1',
                'name' => 'Shift 1',
                'checkin_start' => '07:00',
                'checkin_end' => '13:00',
                'checkout_start' => '13:00',
                'checkout_end' => '14:00',
                'late_deadline' => '08:01',
                'absent_deadline' => '08:30',
            ];
        }

        if ($shift === 'shift_2') {
            if ($isFriday) {
                return [
                    'shift' => 'shift_2',
                    'name' => 'Shift 2 (Friday)',
                    'checkin_start' => '13:30',
                    'checkin_end' => '16:00',
                    'checkout_end' => '20:00',
                    'late_deadline' => '14:00',
                ];
            }

            return [
                'shift' => 'shift_2',
                'name' => 'Shift 2',
                'checkin_start' => '14:00',
                'checkin_end' => '16:00',
                'checkout_end' => '20:00',
                'late_deadline' => '14:45',
            ];
        }

        return null;
    }

    /**
     * Check if clock-in time is within Shift 1 window
     * Shift 1: 7:00 AM - 1:00 PM
     */
    private function isWithinShift1Window($hour, $minute)
    {
        // Before 7:00 AM
        if ($hour < self::SHIFT_1_START_HOUR) {
            return false;
        }

        // After 1:00 PM (13:00)
        if ($hour > self::SHIFT_1_END_HOUR) {
            return false;
        }

        // Between 7:00 AM and 1:00 PM
        if ($hour >= self::SHIFT_1_START_HOUR && $hour <= self::SHIFT_1_END_HOUR) {
            // Exact boundary check: if it's 13:00 or later, it's outside
            if ($hour === self::SHIFT_1_END_HOUR && $minute >= self::SHIFT_1_END_MINUTE) {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Check if clock-in time is within Shift 2 window
     * Shift 2: 2:00 PM - 4:00 PM
     */
    private function isWithinShift2Window($hour, $minute)
    {
        // Before 2:00 PM (14:00)
        if ($hour < self::SHIFT_2_START_HOUR) {
            return false;
        }

        // After 4:00 PM (16:00)
        if ($hour > self::SHIFT_2_END_HOUR) {
            return false;
        }

        // Between 2:00 PM and 4:00 PM
        if ($hour >= self::SHIFT_2_START_HOUR && $hour <= self::SHIFT_2_END_HOUR) {
            // Exact boundary check: if it's 16:00 or later, it's outside
            if ($hour === self::SHIFT_2_END_HOUR && $minute >= self::SHIFT_2_END_MINUTE) {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Check if clock-in time is within Friday Shift 2 window (1:30 PM - 4:00 PM)
     */
    private function isWithinFridayShift2Window($hour, $minute)
    {
        $startHour = self::FRIDAY_SHIFT_2_START_HOUR;
        $startMinute = self::FRIDAY_SHIFT_2_START_MINUTE;
        $endHour = self::SHIFT_2_END_HOUR;
        $endMinute = self::SHIFT_2_END_MINUTE;

        $timeInMinutes = $hour * 60 + $minute;
        $startInMinutes = $startHour * 60 + $startMinute;
        $endInMinutes = $endHour * 60 + $endMinute;

        return $timeInMinutes >= $startInMinutes && $timeInMinutes < $endInMinutes;
    }

    /**
     * Check if clock-in time is within Saturday window (9:00 AM - 1:00 PM)
     */
    private function isWithinSaturdayWindow($hour, $minute)
    {
        if ($hour < self::SATURDAY_START_HOUR) {
            return false;
        }
        if ($hour > self::SATURDAY_END_HOUR) {
            return false;
        }
        if ($hour === self::SATURDAY_END_HOUR && $minute >= self::SATURDAY_END_MINUTE) {
            return false;
        }
        return true;
    }

    /**
     * Check if it is too early to clock out on Saturday (before 12:30 PM)
     */
    public function isSaturdayCheckoutTooEarly(Carbon $clockOutTime)
    {
        $hour = $clockOutTime->hour;
        $minute = $clockOutTime->minute;

        return $hour < self::SATURDAY_CHECKOUT_HOUR ||
               ($hour === self::SATURDAY_CHECKOUT_HOUR && $minute < self::SATURDAY_CHECKOUT_MINUTE);
    }

    /**
     * Get all configured shifts
     */
    public function getAllShifts()
    {
        return [
            [
                'shift' => 'shift_1',
                'name' => 'Shift 1',
                'checkin_start' => '07:00',
                'checkin_end' => '13:00',
                'checkout_start' => '13:00',
                'checkout_end' => '14:00',
                'late_deadline' => '08:01',
                'absent_deadline' => '08:30',
            ],
            [
                'shift' => 'shift_2',
                'name' => 'Shift 2',
                'checkin_start' => '14:00',
                'checkin_end' => '16:00',
                'checkout_end' => '20:00',
                'late_deadline' => '14:45',
            ],
            [
                'shift' => 'shift_2_friday',
                'name' => 'Shift 2 (Friday)',
                'checkin_start' => '13:30',
                'checkin_end' => '16:00',
                'checkout_end' => '20:00',
                'late_deadline' => '14:00',
            ],
            [
                'shift' => 'saturday',
                'name' => 'Saturday',
                'checkin_start' => '09:00',
                'checkin_end' => '13:00',
                'checkout_start' => '12:30',
                'checkout_end' => '13:00',
                'late_deadline' => '09:00',
                'absent_deadline' => '09:30',
            ],
        ];
    }

    /**
     * Detect if employee is absent for a given shift
     * 
     * @param Carbon $currentTime - Current time to check against
     * @param string $shift - 'shift_1' or 'shift_2'
     * 
     * @return array [
     *     'is_absent' => bool,
     *     'reason' => string
     * ]
     */
    public function detectAbsence($currentTime, $shift)
    {
        $hour = $currentTime->hour;
        $minute = $currentTime->minute;

        // Saturday: absent after 9:30 AM with no check-in
        if ($currentTime->isSaturday()) {
            $isAbsent = $hour > self::SATURDAY_ABSENT_HOUR ||
                        ($hour === self::SATURDAY_ABSENT_HOUR && $minute >= self::SATURDAY_ABSENT_MINUTE);

            if ($isAbsent) {
                return [
                    'is_absent' => true,
                    'reason' => 'No check-in by 9:30 AM on Saturday - marked as ABSENT',
                ];
            }

            return ['is_absent' => false, 'reason' => null];
        }

        if ($shift === 'shift_1') {
            $isAbsent = $hour > self::SHIFT_1_ABSENT_HOUR ||
                        ($hour === self::SHIFT_1_ABSENT_HOUR && $minute >= self::SHIFT_1_ABSENT_MINUTE);

            if ($isAbsent) {
                return [
                    'is_absent' => true,
                    'reason' => 'No check-in by 8:30 AM - marked as ABSENT',
                ];
            }
        }

        return [
            'is_absent' => false,
            'reason' => null,
        ];
    }
}
