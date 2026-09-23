{{-- Report Header --}}
<table>
    <tr>
        <td colspan="6" style="background-color: #8b0000; color: #ffffff; font-size: 16px; font-weight: bold; padding: 12px; text-align: center; border: 2px solid #000;">
            All Users Attendance Report
        </td>
    </tr>
</table>

<table>
    <tr>
        <td style="font-weight: bold; padding: 6px; border: 1px solid #ddd; background-color: #f5f5f5;">Report Period</td>
        <td style="padding: 6px; border: 1px solid #ddd;" colspan="5">{{ $startDate->format('d M Y') }} — {{ $endDate->format('d M Y') }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold; padding: 6px; border: 1px solid #ddd; background-color: #f5f5f5;">Total Employees</td>
        <td style="padding: 6px; border: 1px solid #ddd;" colspan="5">{{ $userBlocks->count() }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold; padding: 6px; border: 1px solid #ddd; background-color: #f5f5f5;">Generated</td>
        <td style="padding: 6px; border: 1px solid #ddd;" colspan="5">{{ now()->format('d M Y H:i:s') }}</td>
    </tr>
</table>

<br>

{{-- Overview Table --}}
<table>
    <thead>
        <tr style="background-color: #2196F3; font-weight: bold; color: #ffffff;">
            <th style="border: 2px solid #000; padding: 8px; text-align: center;">#</th>
            <th style="border: 2px solid #000; padding: 8px;">Employee</th>
            <th style="border: 2px solid #000; padding: 8px; text-align: center;">Days Worked</th>
            <th style="border: 2px solid #000; padding: 8px; text-align: center;">Late (S1)</th>
            <th style="border: 2px solid #000; padding: 8px; text-align: center;">Late (S2)</th>
            <th style="border: 2px solid #000; padding: 8px; text-align: center;">Total Late</th>
            <th style="border: 2px solid #000; padding: 8px; text-align: center;">Absent</th>
        </tr>
    </thead>
    <tbody>
        @foreach($userBlocks as $block)
            <tr>
                <td style="border: 1px solid #ddd; padding: 6px; text-align: center;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid #ddd; padding: 6px; font-weight: bold;">{{ $block['user'] ? $block['user']->name : 'Unknown' }}</td>
                <td style="border: 1px solid #ddd; padding: 6px; text-align: center;">{{ $block['daysWorked'] }}</td>
                <td style="border: 1px solid #ddd; padding: 6px; text-align: center; font-weight: bold; {{ $block['lateDaysShift1'] > 0 ? 'background-color: #fff3e0; color: #e65100;' : '' }}">{{ $block['lateDaysShift1'] }}</td>
                <td style="border: 1px solid #ddd; padding: 6px; text-align: center; font-weight: bold; {{ $block['lateDaysShift2'] > 0 ? 'background-color: #fff3e0; color: #e65100;' : '' }}">{{ $block['lateDaysShift2'] }}</td>
                <td style="border: 1px solid #ddd; padding: 6px; text-align: center; font-weight: bold; {{ $block['lateDays'] > 0 ? 'background-color: #ffebee; color: #c62828;' : 'background-color: #e8f5e9; color: #2e7d32;' }}">{{ $block['lateDays'] }}</td>
                <td style="border: 1px solid #ddd; padding: 6px; text-align: center; font-weight: bold; {{ $block['absentDays'] > 0 ? 'background-color: #fce4ec; color: #880e4f;' : 'background-color: #e8f5e9; color: #2e7d32;' }}">{{ $block['absentDays'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<br>
<br>

{{-- Per-User Detailed Sections --}}
@foreach($userBlocks as $block)
    <table>
        <tr>
            <td colspan="6" style="background-color: #37474f; color: #ffffff; font-weight: bold; padding: 10px; border: 2px solid #000;">
                {{ $block['user'] ? $block['user']->name : 'Unknown' }} (ID: {{ $block['user'] ? $block['user']->id : 'N/A' }})
                &nbsp;&nbsp;|&nbsp;&nbsp; Days: {{ $block['daysWorked'] }}
                &nbsp;&nbsp;|&nbsp;&nbsp; Late S1: {{ $block['lateDaysShift1'] }}
                &nbsp;&nbsp;|&nbsp;&nbsp; Late S2: {{ $block['lateDaysShift2'] }}
                &nbsp;&nbsp;|&nbsp;&nbsp; Total Late: {{ $block['lateDays'] }}
                &nbsp;&nbsp;|&nbsp;&nbsp; Absent: {{ $block['absentDays'] }}
            </td>
        </tr>
    </table>
    <table>
        <thead>
            <tr style="background-color: #8b0000; font-weight: bold; color: #ffffff;">
                <th style="border: 1px solid #000; padding: 6px; text-align: center;">Date</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center;">Day</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center;">Clock In</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center;">Clock Out</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center;">Shift</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $today = \Carbon\Carbon::today();
                $rangeEnd = $endDate->copy()->lt($today) ? $endDate->copy() : $today->copy();
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
                @if(isset($block['sessionsByDate'][$dateStr]))
                    @foreach($block['sessionsByDate'][$dateStr]->groupBy('shift_window')->sortKeys() as $shiftKey => $shiftSessions)
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
                            <td style="border: 1px solid #ddd; padding: 5px; background-color: {{ $rowBg }};">{{ $firstSession->session_date->format('Y-m-d') }}</td>
                            <td style="border: 1px solid #ddd; padding: 5px; background-color: {{ $rowBg }};">{{ $firstSession->session_date->format('l') }}</td>
                            <td style="border: 1px solid #ddd; padding: 5px; text-align: center; background-color: {{ $rowBg }};">{{ $firstSession->clock_in_time->format('h:i:s A') }}</td>
                            <td style="border: 1px solid #ddd; padding: 5px; text-align: center; background-color: {{ $rowBg }};">{{ $lastSession->clock_out_time ? $lastSession->clock_out_time->format('h:i:s A') : 'N/A' }}</td>
                            <td style="border: 1px solid #ddd; padding: 5px; text-align: center; background-color: {{ $rowBg }};">{{ $shiftLabel }}</td>
                            <td style="border: 1px solid #ddd; padding: 5px; text-align: center; font-weight: bold; {{ $statusStyle }}">{{ $statusLabel }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 5px; background-color: #f8d7da;">{{ $dateStr }}</td>
                        <td style="border: 1px solid #ddd; padding: 5px; background-color: #f8d7da;">{!! \Carbon\Carbon::parse($dateStr)->format('l') !!}</td>
                        <td style="border: 1px solid #ddd; padding: 5px; text-align: center; background-color: #f8d7da; color: #721c24;">—</td>
                        <td style="border: 1px solid #ddd; padding: 5px; text-align: center; background-color: #f8d7da; color: #721c24;">—</td>
                        <td style="border: 1px solid #ddd; padding: 5px; text-align: center; background-color: #f8d7da; color: #721c24;">—</td>
                        <td style="border: 1px solid #ddd; padding: 5px; text-align: center; font-weight: bold; background-color: #f8d7da; color: #721c24;">Absent</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    <br>
@endforeach
