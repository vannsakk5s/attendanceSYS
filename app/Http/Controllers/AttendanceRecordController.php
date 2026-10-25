<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class AttendanceRecordController extends Controller
{
    public function updateBatch(Request $request, AttendanceSession $session): RedirectResponse
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
}
