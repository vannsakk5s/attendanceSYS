<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AttendanceSessionController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();
        $classIds = SchoolClass::where('teacher_id', $teacher->id)->pluck('id');

        $query = AttendanceSession::whereIn('class_id', $classIds)
            ->with(['schoolClass', 'attendanceRecords'])
            ->latest('session_date')
            ->latest('start_time');

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->input('class_id'));
        }

        if ($request->filled('date')) {
            $query->whereDate('session_date', $request->input('date'));
        }

        $sessions = $query->paginate(12)->withQueryString();
        $classes = SchoolClass::where('teacher_id', $teacher->id)->orderBy('class_name')->get();

        return view('sessions.index', compact('sessions', 'classes'));
    }

    public function create(Request $request): View
    {
        $teacher = $request->user();
        $classes = SchoolClass::where('teacher_id', $teacher->id)
            ->withCount('students')
            ->with(['schedules' => function ($query) {
                $query->orderBy('day_of_week')->orderBy('start_time');
            }])
            ->orderBy('class_name')
            ->get();

        $selectedClassId = $request->input('class_id');
        $defaultDate = now()->format('Y-m-d');
        $defaultStartTime = now()->format('H:i');
        $defaultEndTime = now()->addMinutes(90)->format('H:i');

        return view('sessions.create', compact('classes', 'selectedClassId', 'defaultDate', 'defaultStartTime', 'defaultEndTime'));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = $request->user();

        $validated = $request->validate([
            'class_id' => [
                'required',
                Rule::exists('classes', 'id')->where('teacher_id', $teacher->id),
            ],
            'session_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $class = SchoolClass::with('students')->findOrFail($validated['class_id']);

        $session = AttendanceSession::create([
            'class_id' => $class->id,
            'session_date' => $validated['session_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);

        // Auto initialize attendance records for every student in the class
        foreach ($class->students as $student) {
            AttendanceRecord::firstOrCreate(
                [
                    'attendance_session_id' => $session->id,
                    'student_id' => $student->id,
                ],
                [
                    'status' => AttendanceRecord::STATUS_PRESENT,
                    'note' => null,
                ]
            );
        }

        return redirect()->route('sessions.show', $session)
            ->with('success', "Attendance session created for {$class->class_name} on {$session->session_date->format('M d, Y')}. Ready to mark attendance.");
    }

    public function show(AttendanceSession $session): View
    {
        Gate::authorize('view', $session);

        $session->load([
            'schoolClass',
            'attendanceRecords' => function ($query) {
                $query->join('students', 'attendance_records.student_id', '=', 'students.id')
                    ->orderBy('students.student_code')
                    ->select('attendance_records.*');
            },
            'attendanceRecords.student',
        ]);

        return view('sessions.show', compact('session'));
    }

    public function updateRecords(Request $request, AttendanceSession $session): RedirectResponse
    {
        Gate::authorize('update', $session);

        $validated = $request->validate([
            'records' => ['required', 'array'],
            'records.*.status' => ['required', Rule::in(AttendanceRecord::STATUSES)],
            'records.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validated['records'] as $recordId => $recordData) {
            AttendanceRecord::where('id', $recordId)
                ->where('attendance_session_id', $session->id)
                ->update([
                    'status' => $recordData['status'],
                    'note' => $recordData['note'] ?? null,
                ]);
        }

        return redirect()->route('sessions.show', $session)
            ->with('success', 'Attendance records have been updated successfully.');
    }

    public function destroy(AttendanceSession $session): RedirectResponse
    {
        Gate::authorize('delete', $session);

        $classId = $session->class_id;
        $session->delete();

        return redirect()->route('classes.show', [$classId, 'tab' => 'sessions'])
            ->with('success', 'Attendance session deleted.');
    }

    public function regenerateShareLink(AttendanceSession $session): RedirectResponse
    {
        Gate::authorize('update', $session);

        $session->update([
            'share_token' => \Illuminate\Support\Str::random(32),
        ]);

        return back()->with('success', 'Student attendance session link has been regenerated successfully.');
    }
}

