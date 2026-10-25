<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\ClassSchedule;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();
        $classIds = SchoolClass::where('teacher_id', $teacher->id)->pluck('id');

        $totalClasses = $classIds->count();
        $totalStudents = Student::whereIn('class_id', $classIds)->count();

        $sessionIds = AttendanceSession::whereIn('class_id', $classIds)->pluck('id');
        $totalSessions = $sessionIds->count();

        // Attendance stats
        $totalRecords = AttendanceRecord::whereIn('attendance_session_id', $sessionIds)->count();
        $attendedRecords = AttendanceRecord::whereIn('attendance_session_id', $sessionIds)
            ->whereIn('status', ['present', 'late'])
            ->count();

        $overallAttendanceRate = $totalRecords > 0
            ? round(($attendedRecords / $totalRecords) * 100, 1)
            : 0;

        // Today's schedule
        $todayDayOfWeek = now()->format('l'); // 'Monday', 'Tuesday', etc.
        $todaySchedules = ClassSchedule::whereIn('class_id', $classIds)
            ->where('day_of_week', $todayDayOfWeek)
            ->with('schoolClass')
            ->orderBy('start_time')
            ->get();

        // Recent Sessions
        $recentSessions = AttendanceSession::whereIn('class_id', $classIds)
            ->with(['schoolClass', 'attendanceRecords'])
            ->latest('session_date')
            ->latest('start_time')
            ->limit(5)
            ->get();

        // Classes overview
        $classes = SchoolClass::where('teacher_id', $teacher->id)
            ->withCount(['students', 'attendanceSessions'])
            ->with('schedules')
            ->orderBy('class_name')
            ->get();

        return view('dashboard', compact(
            'teacher',
            'totalClasses',
            'totalStudents',
            'totalSessions',
            'overallAttendanceRate',
            'todayDayOfWeek',
            'todaySchedules',
            'recentSessions',
            'classes'
        ));
    }
}
