<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ClassScheduleController extends Controller
{
    public function store(Request $request, SchoolClass $class): RedirectResponse
    {
        Gate::authorize('update', $class);

        $validated = $request->validate([
            'day_of_week' => ['required', 'string', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            'shift' => ['required', 'string', 'max:50'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $class->schedules()->create($validated);

        return redirect()->route('classes.show', [$class, 'tab' => 'schedules'])
            ->with('success', "Schedule added for {$validated['day_of_week']}.");
    }

    public function destroy(SchoolClass $class, ClassSchedule $schedule): RedirectResponse
    {
        Gate::authorize('update', $class);

        if ($schedule->class_id !== $class->id) {
            abort(404);
        }

        $schedule->delete();

        return redirect()->route('classes.show', [$class, 'tab' => 'schedules'])
            ->with('success', 'Schedule removed successfully.');
    }
}
