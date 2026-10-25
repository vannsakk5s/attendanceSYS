<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();

        $classes = SchoolClass::where('teacher_id', $teacher->id)
            ->withCount(['students', 'schedules', 'attendanceSessions'])
            ->with(['schedules', 'attendanceSessions' => function ($query) {
                $query->latest('session_date')->limit(5)->with('attendanceRecords');
            }])
            ->orderBy('class_name')
            ->get();

        return view('classes.index', compact('classes'));
    }

    public function create(): View
    {
        return view('classes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_name' => ['required', 'string', 'max:150'],
            'subject_name' => ['required', 'string', 'max:150'],
        ]);

        $class = SchoolClass::create([
            'teacher_id' => $request->user()->id,
            'class_name' => $validated['class_name'],
            'subject_name' => $validated['subject_name'],
        ]);

        return redirect()->route('classes.show', $class)
            ->with('success', "Class '{$class->class_name}' ({$class->subject_name}) created successfully.");
    }

    public function show(SchoolClass $class, Request $request): View
    {
        Gate::authorize('view', $class);

        $class->load([
            'students' => function ($query) {
                $query->orderBy('student_code');
            },
            'schedules' => function ($query) {
                $query->orderBy('day_of_week')->orderBy('start_time');
            },
            'attendanceSessions' => function ($query) {
                $query->latest('session_date')->latest('start_time')->with('attendanceRecords');
            },
        ]);

        return view('classes.show', compact('class'));
    }

    public function edit(SchoolClass $class): View
    {
        Gate::authorize('update', $class);

        return view('classes.edit', compact('class'));
    }

    public function update(Request $request, SchoolClass $class): RedirectResponse
    {
        Gate::authorize('update', $class);

        $validated = $request->validate([
            'class_name' => ['required', 'string', 'max:150'],
            'subject_name' => ['required', 'string', 'max:150'],
        ]);

        $class->update($validated);

        return redirect()->route('classes.show', $class)
            ->with('success', 'Class updated successfully.');
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        Gate::authorize('delete', $class);

        $name = $class->class_name;
        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', "Class '{$name}' has been deleted.");
    }

    public function regenerateShareLink(SchoolClass $class): RedirectResponse
    {
        Gate::authorize('update', $class);

        $class->update([
            'share_token' => \Illuminate\Support\Str::random(32),
        ]);

        return back()->with('success', 'Student shareable attendance link has been regenerated successfully.');
    }
}
