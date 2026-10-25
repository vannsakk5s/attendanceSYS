<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicAttendanceController extends Controller
{
    public function view(string $token, Request $request): View
    {
        $class = SchoolClass::where('share_token', $token)
            ->with([
                'teacher',
                'schedules',
                'students' => fn ($q) => $q->orderBy('student_code'),
                'attendanceSessions' => fn ($q) => $q->orderBy('session_date', 'desc')->orderBy('start_time', 'desc')->with('attendanceRecords'),
            ])
            ->firstOrFail();

        $sessions = $class->attendanceSessions;
        $sessionIds = $sessions->pluck('id');

        $studentsData = $class->students->map(function ($student) use ($sessionIds) {
            $records = AttendanceRecord::whereIn('attendance_session_id', $sessionIds)
                ->where('student_id', $student->id)
                ->with('attendanceSession')
                ->get();

            $present = $records->where('status', 'present')->count();
            $late = $records->where('status', 'late')->count();
            $absent = $records->where('status', 'absent')->count();
            $excused = $records->where('status', 'excused')->count();
            $total = $records->count();

            $rate = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;

            return [
                'student' => $student,
                'present' => $present,
                'late' => $late,
                'absent' => $absent,
                'excused' => $excused,
                'total' => $total,
                'rate' => $rate,
                'records' => $records->sortByDesc(fn ($r) => $r->attendanceSession->session_date->format('Y-m-d') . ' ' . $r->attendanceSession->start_time)->values(),
            ];
        });

        // Search or direct student code match
        $searchCode = $request->input('student_code');
        $activeStudent = null;
        if ($searchCode) {
            $activeStudent = $studentsData->first(function ($item) use ($searchCode) {
                return strtolower($item['student']->student_code) === strtolower(trim($searchCode));
            });
        }

        return view('public.class_attendance', compact('class', 'sessions', 'studentsData', 'activeStudent', 'searchCode'));
    }

    public function viewSession(string $token, Request $request): View
    {
        $session = \App\Models\AttendanceSession::where('share_token', $token)
            ->with([
                'schoolClass.teacher',
                'attendanceRecords.student',
            ])
            ->firstOrFail();

        $class = $session->schoolClass;
        $records = $session->attendanceRecords->sortBy(fn ($r) => $r->student->student_code)->values();
        $search = $request->input('search');

        return view('public.session_attendance', compact('session', 'class', 'records', 'search'));
    }
}
