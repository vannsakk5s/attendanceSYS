<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Attendance Report - {{ $class->class_name }}</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; font-size: 12px; color: #1e293b; padding: 24px; line-height: 1.4; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #0f172a; padding-bottom: 12px; margin-bottom: 16px; }
        .title { font-size: 18px; font-weight: bold; margin: 0; }
        .sub { font-size: 12px; color: #64748b; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 11px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background-color: #f1f5f9; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { margin-top: 40px; display: flex; justify-content: space-between; }
        .sig-line { width: 180px; border-top: 1px solid #000; margin-top: 40px; text-align: center; font-size: 11px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 16px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #4f46e5; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            Print / Save to PDF
        </button>
    </div>

    <div class="header">
        <div>
            <h1 class="title">OFFICIAL ATTENDANCE REPORT</h1>
            <p class="sub">Class: <strong>{{ $class->class_name }}</strong> • Subject: <strong>{{ $class->subject_name }}</strong></p>
        </div>
        <div style="text-align: right;">
            <p class="sub">Teacher: <strong>{{ $class->teacher->name }}</strong></p>
            <p class="sub">Report Generated: <strong>{{ now()->format('Y-m-d H:i') }}</strong></p>
        </div>
    </div>

    <div style="margin-bottom: 12px; font-size: 11px; color: #475569;">
        Total Sessions Conducted: <strong>{{ count($reportData['sessions']) }}</strong> | Total Students: <strong>{{ count($reportData['students']) }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th style="width: 90px;">Student Code</th>
                <th>Full Name</th>
                <th class="text-center" style="width: 50px;">Present</th>
                <th class="text-center" style="width: 50px;">Late</th>
                <th class="text-center" style="width: 50px;">Absent</th>
                <th class="text-center" style="width: 50px;">Excused</th>
                <th class="text-center" style="width: 50px;">Total</th>
                <th class="text-right" style="width: 70px;">Attendance %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['students'] as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td style="font-family: monospace;">{{ $item['student']->student_code }}</td>
                    <td><strong>{{ $item['student']->full_name }}</strong></td>
                    <td class="text-center">{{ $item['present'] }}</td>
                    <td class="text-center">{{ $item['late'] }}</td>
                    <td class="text-center">{{ $item['absent'] }}</td>
                    <td class="text-center">{{ $item['excused'] }}</td>
                    <td class="text-center">{{ $item['total_sessions'] }}</td>
                    <td class="text-right"><strong>{{ $item['rate'] }}%</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div>
            <p>Prepared by:</p>
            <div class="sig-line">{{ $class->teacher->name }}<br>(Teacher Signature)</div>
        </div>
        <div>
            <p>Approved by:</p>
            <div class="sig-line">Academic Office<br>(Stamp / Signature)</div>
        </div>
    </div>
</body>
</html>
