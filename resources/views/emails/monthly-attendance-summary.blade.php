<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="750" cellpadding="0" cellspacing="0" style="background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

                    {{-- Header --}}
                    <tr>
                        <td style="background-color: #8b0000; color: #ffffff; padding: 20px 30px;">
                            <h1 style="margin: 0; font-size: 22px; font-weight: bold;">Monthly Attendance Summary</h1>
                            <p style="margin: 6px 0 0; font-size: 14px; opacity: 0.9;">{{ $data['month'] }} {{ $data['year'] }} &mdash; {{ $data['start'] }} to {{ $data['end'] }}</p>
                        </td>
                    </tr>

                    {{-- Summary Box --}}
                    <tr>
                        <td style="padding: 20px 30px 10px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f9f9f9; border-left: 4px solid #8b0000; border-radius: 4px;">
                                <tr>
                                    <td style="padding: 14px 18px; font-size: 14px; color: #333;">
                                        <strong style="color: #8b0000;">Report Period:</strong> {{ $data['month'] }} {{ $data['year'] }}<br>
                                        <strong style="color: #8b0000;">Total Employees:</strong> {{ $data['users']->count() }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Data Table --}}
                    <tr>
                        <td style="padding: 10px 30px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-size: 13px;">
                                <thead>
                                    <tr>
                                        <th style="background-color: #8b0000; color: #ffffff; padding: 10px 8px; text-align: center; border: 1px solid #7a0000; width: 35px;">#</th>
                                        <th style="background-color: #8b0000; color: #ffffff; padding: 10px 10px; text-align: left; border: 1px solid #7a0000;">Employee</th>
                                        <th style="background-color: #8b0000; color: #ffffff; padding: 10px 8px; text-align: center; border: 1px solid #7a0000; width: 95px;">Late (S1)</th>
                                        <th style="background-color: #8b0000; color: #ffffff; padding: 10px 8px; text-align: center; border: 1px solid #7a0000; width: 95px;">Late (S2)</th>
                                        <th style="background-color: #8b0000; color: #ffffff; padding: 10px 8px; text-align: center; border: 1px solid #7a0000; width: 85px;">Absent Days</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['users'] as $index => $user)
                                    @php
                                        $rowBg = $index % 2 === 0 ? '#ffffff' : '#f8f8f8';
                                    @endphp
                                    <tr>
                                        <td style="padding: 9px 8px; text-align: center; border: 1px solid #e0e0e0; background-color: {{ $rowBg }};">{{ $index + 1 }}</td>
                                        <td style="padding: 9px 10px; border: 1px solid #e0e0e0; background-color: {{ $rowBg }}; font-weight: bold;">{{ $user['user_name'] }}</td>
                                        <td style="padding: 9px 8px; text-align: center; border: 1px solid #e0e0e0; font-weight: bold;
                                            @if($user['morning_late'] > 0)
                                                background-color: #fff3cd; color: #856404;
                                            @else
                                                background-color: #d4edda; color: #155724;
                                            @endif
                                        ">{{ $user['morning_late'] > 0 ? $user['morning_late'] : 0 }}</td>
                                        <td style="padding: 9px 8px; text-align: center; border: 1px solid #e0e0e0; font-weight: bold;
                                            @if($user['evening_late'] > 0)
                                                background-color: #fff3cd; color: #856404;
                                            @else
                                                background-color: #d4edda; color: #155724;
                                            @endif
                                        ">{{ $user['evening_late'] > 0 ? $user['evening_late'] : 0 }}</td>
                                        <td style="padding: 9px 8px; text-align: center; border: 1px solid #e0e0e0; font-weight: bold;
                                            @if($user['total_absent'] > 0)
                                                background-color: #f8d7da; color: #721c24;
                                            @else
                                                background-color: #d4edda; color: #155724;
                                            @endif
                                        ">{{ $user['total_absent'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f8f8f8; padding: 16px 30px; font-size: 12px; color: #888; border-top: 1px solid #eee;">
                            This is an automated report generated by FTS Portal on {{ now()->format('Y-m-d H:i:s') }}.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
