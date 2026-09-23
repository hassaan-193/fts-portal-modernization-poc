{{-- Report Header --}}
<table>
    <tr>
        <td colspan="5" style="background-color: #8b0000; color: #ffffff; font-size: 16px; font-weight: bold; padding: 12px; text-align: center; border: 2px solid #000;">
            Attendance Report
        </td>
    </tr>
</table>

<table>
    <tr>
        <td style="font-weight: bold; padding: 6px; border: 1px solid #ddd; background-color: #f5f5f5;">Employee Name</td>
        <td style="padding: 6px; border: 1px solid #ddd;" colspan="4">{{ $user->name }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold; padding: 6px; border: 1px solid #ddd; background-color: #f5f5f5;">Employee ID</td>
        <td style="padding: 6px; border: 1px solid #ddd;" colspan="4">{{ $user->id }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold; padding: 6px; border: 1px solid #ddd; background-color: #f5f5f5;">Report Period</td>
        <td style="padding: 6px; border: 1px solid #ddd;" colspan="4">{{ $startDate->format('d M Y') }} — {{ $endDate->format('d M Y') }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold; padding: 6px; border: 1px solid #ddd; background-color: #f5f5f5;">Generated</td>
        <td style="padding: 6px; border: 1px solid #ddd;" colspan="4">{{ now()->format('d M Y H:i:s') }}</td>
    </tr>
</table>

<br>

{{-- Summary Statistics --}}
<table>
    <tr style="background-color: #2196F3; font-weight: bold; color: #ffffff;">
        <td colspan="4" style="border: 2px solid #000; padding: 8px;">Summary</td>
    </tr>
    <tr>
        <td style="border: 1px solid #ddd; padding: 6px; background-color: #e3f2fd; font-weight: bold;">Days Worked</td>
        <td style="border: 1px solid #ddd; padding: 6px; text-align: center; font-weight: bold;">{{ $groupedByDate->count() }}</td>
        <td style="border: 1px solid #ddd; padding: 6px; background-color: #e3f2fd; font-weight: bold;">Absent Days</td>
        <td style="border: 1px solid #ddd; padding: 6px; text-align: center; font-weight: bold; {{ $absentDays > 0 ? 'background-color: #ffebee; color: #c62828;' : 'background-color: #e8f5e9; color: #2e7d32;' }}">{{ $absentDays }}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #ddd; padding: 6px; background-color: #e3f2fd; font-weight: bold;">Late (Shift 1)</td>
        <td style="border: 1px solid #ddd; padding: 6px; text-align: center; font-weight: bold; {{ $lateDaysShift1 > 0 ? 'background-color: #fff3e0; color: #e65100;' : '' }}">{{ $lateDaysShift1 }}</td>
        <td style="border: 1px solid #ddd; padding: 6px; background-color: #e3f2fd; font-weight: bold;">Late (Shift 2)</td>
        <td style="border: 1px solid #ddd; padding: 6px; text-align: center; font-weight: bold; {{ $lateDaysShift2 > 0 ? 'background-color: #fff3e0; color: #e65100;' : '' }}">{{ $lateDaysShift2 }}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #ddd; padding: 6px; background-color: #e3f2fd; font-weight: bold;">Total Late Days</td>
        <td style="border: 1px solid #ddd; padding: 6px; text-align: center; font-weight: bold; {{ $lateDays > 0 ? 'background-color: #ffebee; color: #c62828;' : 'background-color: #e8f5e9; color: #2e7d32;' }}" colspan="3">{{ $lateDays }}</td>
    </tr>
</table>

<br>

{{-- Daily Records --}}
<table>
    <thead>
        <tr style="background-color: #8b0000; font-weight: bold; color: #ffffff;">
            <th style="border: 2px solid #000; padding: 8px; text-align: center;">Date</th>
            <th style="border: 2px solid #000; padding: 8px; text-align: center;">Day</th>
            <th style="border: 2px solid #000; padding: 8px; text-align: center;">Clock In</th>
            <th style="border: 2px solid #000; padding: 8px; text-align: center;">Clock Out</th>
            <th style="border: 2px solid #000; padding: 8px; text-align: center;">Shift</th>
            <th style="border: 2px solid #000; padding: 8px; text-align: center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @php
            $allWeekdays = collect();
            $checkDate = $startDate->copy();
            while ($checkDate->lte($rangeEnd)) {
                if (!$checkDate->isSunday()) {
                    $allWeekdays->push($checkDate->format('Y-m-d'));
                }
                $checkDate->addDay();
            }
        @endphp
        @foreach($allWeekdays as $dateStr)
            @if($groupedByDate->has($dateStr))
                @foreach($groupedByDate[$dateStr]->groupBy('shift_window')->sortKeys() as $shiftKey => $shiftSessions)
                    @php
                        $firstSession = $shiftSessions->sortBy('clock_in_time')->first();
                        $lastSession  = $shiftSessions->sortBy('clock_in_time')->last();
                        $isLate = $firstSession->is_late;
                        $isSat = $firstSession->session_date->isSaturday();
                        if ($isSat) {
                            $shiftLabel = 'Saturday';
                        } else {
                            $shiftLabel = strtoupper(str_replace('shift_', 'Shift ', $shiftKey));
                        }
                        if ($isLate) {
                            $statusLabel = 'Late (' . ($shiftKey === 'shift_1' ? 'S1' : 'S2') . ')';
                            $statusStyle = 'background-color: #fff3e0; color: #e65100;';
                            $rowBg = '#fff3e0';
                        } else {
                            $statusLabel = 'On Time';
                            $statusStyle = 'background-color: #c8e6c9; color: #1b5e20;';
                            $rowBg = '#ffffff';
                        }
                    @endphp
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 6px; background-color: {{ $rowBg }};">{{ $firstSession->session_date->format('Y-m-d') }}</td>
                        <td style="border: 1px solid #ddd; padding: 6px; background-color: {{ $rowBg }};">{{ $firstSession->session_date->format('l') }}</td>
                        <td style="border: 1px solid #ddd; padding: 6px; text-align: center; background-color: {{ $rowBg }};">{{ $firstSession->clock_in_time->format('h:i:s A') }}</td>
                        <td style="border: 1px solid #ddd; padding: 6px; text-align: center; background-color: {{ $rowBg }};">{{ $lastSession->clock_out_time ? $lastSession->clock_out_time->format('h:i:s A') : 'N/A' }}</td>
                        <td style="border: 1px solid #ddd; padding: 6px; text-align: center; background-color: {{ $rowBg }};">{{ $shiftLabel }}</td>
                        <td style="border: 1px solid #ddd; padding: 6px; text-align: center; font-weight: bold; {{ $statusStyle }}">{{ $statusLabel }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td style="border: 1px solid #ddd; padding: 6px; background-color: #f8d7da;">{{ $dateStr }}</td>
                    <td style="border: 1px solid #ddd; padding: 6px; background-color: #f8d7da;">{!! \Carbon\Carbon::parse($dateStr)->format('l') !!}</td>
                    <td style="border: 1px solid #ddd; padding: 6px; text-align: center; background-color: #f8d7da; color: #721c24;">—</td>
                    <td style="border: 1px solid #ddd; padding: 6px; text-align: center; background-color: #f8d7da; color: #721c24;">—</td>
                    <td style="border: 1px solid #ddd; padding: 6px; text-align: center; background-color: #f8d7da; color: #721c24;">—</td>
                    <td style="border: 1px solid #ddd; padding: 6px; text-align: center; font-weight: bold; background-color: #f8d7da; color: #721c24;">Absent</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>

