<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();

        $classes = SchoolClass::where('teacher_id', $teacher->id)
            ->withCount('students')
            ->with(['attendanceSessions.attendanceRecords'])
            ->orderBy('class_name')
            ->get()
            ->map(function ($cls) {
                $totalSessions = $cls->attendanceSessions->count();
                $allRecords = $cls->attendanceSessions->flatMap->attendanceRecords;
                $totalRecords = $allRecords->count();
                $attendedRecords = $allRecords->whereIn('status', ['present', 'late'])->count();

                $rate = $totalRecords > 0 ? round(($attendedRecords / $totalRecords) * 100, 1) : 0;

                return [
                    'class' => $cls,
                    'total_sessions' => $totalSessions,
                    'total_students' => $cls->students_count,
                    'attendance_rate' => $rate,
                ];
            });

        return view('reports.index', compact('classes'));
    }

    public function show(SchoolClass $class, Request $request): View
    {
        Gate::authorize('view', $class);

        $class->load([
            'students' => fn ($q) => $q->orderBy('student_code'),
            'attendanceSessions' => fn ($q) => $q->orderBy('session_date')->orderBy('start_time')->with('attendanceRecords'),
        ]);

        $reportData = $this->buildClassReportData($class);

        return view('reports.show', compact('class', 'reportData'));
    }

    public function exportCsv(SchoolClass $class): Response
    {
        Gate::authorize('view', $class);

        $class->load([
            'students' => fn ($q) => $q->orderBy('student_code'),
            'attendanceSessions' => fn ($q) => $q->orderBy('session_date')->orderBy('start_time')->with('attendanceRecords'),
        ]);

        $reportData = $this->buildClassReportData($class);

        $csv = "Student Code,Student Name,Present,Late,Absent,Excused,Total Sessions,Attendance Rate (%)\n";

        foreach ($reportData['students'] as $item) {
            $csv .= sprintf(
                "\"%s\",\"%s\",%d,%d,%d,%d,%d,%.1f%%\n",
                str_replace('"', '""', $item['student']->student_code),
                str_replace('"', '""', $item['student']->full_name),
                $item['present'],
                $item['late'],
                $item['absent'],
                $item['excused'],
                $item['total_sessions'],
                $item['rate']
            );
        }

        $filename = "attendance_report_{$class->class_name}_" . now()->format('Ymd_His') . ".csv";

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function print(SchoolClass $class): View
    {
        Gate::authorize('view', $class);

        $class->load([
            'students' => fn ($q) => $q->orderBy('student_code'),
            'attendanceSessions' => fn ($q) => $q->orderBy('session_date')->orderBy('start_time')->with('attendanceRecords'),
        ]);

        $reportData = $this->buildClassReportData($class);

        return view('reports.print', compact('class', 'reportData'));
    }

    private function buildClassReportData(SchoolClass $class): array
    {
        $sessions = $class->attendanceSessions;
        $students = $class->students;

        $studentStats = [];
        foreach ($students as $student) {
            $studentRecords = AttendanceRecord::whereIn('attendance_session_id', $sessions->pluck('id'))
                ->where('student_id', $student->id)
                ->get();

            $present = $studentRecords->where('status', 'present')->count();
            $late = $studentRecords->where('status', 'late')->count();
            $absent = $studentRecords->where('status', 'absent')->count();
            $excused = $studentRecords->where('status', 'excused')->count();
            $total = $present + $late + $absent + $excused;
            $rate = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;

            $sessionMap = [];
            foreach ($studentRecords as $rec) {
                $sessionMap[$rec->attendance_session_id] = [
                    'status' => $rec->status,
                    'note' => $rec->note,
                ];
            }

            $studentStats[] = [
                'student' => $student,
                'present' => $present,
                'late' => $late,
                'absent' => $absent,
                'excused' => $excused,
                'total_sessions' => $total,
                'rate' => $rate,
                'session_map' => $sessionMap,
            ];
        }

        return [
            'sessions' => $sessions,
            'students' => $studentStats,
        ];
    }
}
