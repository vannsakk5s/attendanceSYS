<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Student;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Get eligible students for an assignment session.
     * In the ERD, a classroom contains students, and teaching assignment is for a classroom.
     */
    public function getEligibleStudents(TeachingAssignment $teachingAssignment): Collection
    {
        return $teachingAssignment->classroom
            ->students()
            ->whereNull('archived_at')
            ->orderBy('full_name')
            ->get();
    }

    /**
     * Create a new attendance session with date and time validations.
     */
    public function createSession(TeachingAssignment $teachingAssignment, array $data, User $teacher): AttendanceSession
    {
        $sessionDate = $data['session_date'];
        $startTime = $data['start_time'];
        $endTime = $data['end_time'];

        // Validate session date is within teaching assignment schedule
        $assignmentStart = $teachingAssignment->start_date->toDateString();
        $assignmentEnd = $teachingAssignment->end_date->toDateString();

        if ($sessionDate < $assignmentStart || $sessionDate > $assignmentEnd) {
            throw ValidationException::withMessages([
                'session_date' => "Session date must be within assignment schedule ({$assignmentStart} to {$assignmentEnd}).",
            ]);
        }

        // Validate end_time is later than start_time
        if ($endTime <= $startTime) {
            throw ValidationException::withMessages([
                'end_time' => 'End time must be later than start time.',
            ]);
        }

        // Check unique constraint: (teaching_assignment_id, session_date, start_time)
        $normalizedTime = strlen($startTime) === 5 ? $startTime . ':00' : $startTime;
        $exists = AttendanceSession::where('teaching_assignment_id', $teachingAssignment->id)
            ->whereDate('session_date', $sessionDate)
            ->where(function ($q) use ($startTime, $normalizedTime) {
                $q->where('start_time', $startTime)
                    ->orWhere('start_time', $normalizedTime);
            })
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'start_time' => 'A session for this assignment on this date with this start time already exists.',
            ]);
        }

        return DB::transaction(function () use ($teachingAssignment, $data, $teacher) {
            $session = AttendanceSession::create([
                'teaching_assignment_id' => $teachingAssignment->id,
                'session_date' => $data['session_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'status' => AttendanceSession::STATUS_OPEN,
                'notes' => $data['notes'] ?? null,
            ]);

            // Automatically initialize attendance records as 'present' or unpopulated for all active students in classroom
            $students = $this->getEligibleStudents($teachingAssignment);
            foreach ($students as $student) {
                AttendanceRecord::create([
                    'attendance_session_id' => $session->id,
                    'student_id' => $student->id,
                    'status' => AttendanceRecord::STATUS_PRESENT,
                    'remark' => null,
                ]);
            }

            $this->auditLogService->log(
                action: 'create_attendance_session',
                entity: $session,
                entityId: $session->id,
                oldValues: null,
                newValues: [
                    'session_date' => $session->session_date->toDateString(),
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                ],
                actorId: $teacher->id
            );

            return $session;
        });
    }

    /**
     * Save attendance records batch inside a transaction with row locking.
     */
    public function recordBatch(AttendanceSession $session, array $recordsData, User $teacher): void
    {
        DB::transaction(function () use ($session, $recordsData, $teacher) {
            /** @var AttendanceSession $lockedSession */
            $lockedSession = AttendanceSession::where('id', $session->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedSession->isOpen()) {
                throw ValidationException::withMessages([
                    'session' => 'Cannot update attendance: this session is not open.',
                ]);
            }

            $eligibleStudentIds = $this->getEligibleStudents($lockedSession->teachingAssignment)
                ->pluck('id')
                ->all();

            $existingRecords = AttendanceRecord::where('attendance_session_id', $lockedSession->id)
                ->get()
                ->keyBy('student_id');

            $auditChanges = [];

            foreach ($recordsData as $row) {
                $studentId = (int) $row['student_id'];
                $status = $row['status'];
                $remark = $row['remark'] ?? null;

                // Validate student is eligible
                if (! in_array($studentId, $eligibleStudentIds, true)) {
                    continue;
                }

                $record = $existingRecords->get($studentId);

                if ($record) {
                    $oldStatus = $record->status;
                    $oldRemark = $record->remark;

                    if ($oldStatus !== $status || $oldRemark !== $remark) {
                        $record->update([
                            'status' => $status,
                            'remark' => $remark,
                        ]);

                        $auditChanges[$studentId] = [
                            'student_id' => $studentId,
                            'from' => $oldStatus,
                            'to' => $status,
                            'remark' => $remark,
                        ];
                    }
                } else {
                    AttendanceRecord::create([
                        'attendance_session_id' => $lockedSession->id,
                        'student_id' => $studentId,
                        'status' => $status,
                        'remark' => $remark,
                    ]);

                    $auditChanges[$studentId] = [
                        'student_id' => $studentId,
                        'from' => null,
                        'to' => $status,
                        'remark' => $remark,
                    ];
                }
            }

            if (! empty($auditChanges)) {
                $this->auditLogService->log(
                    action: 'update_attendance_records',
                    entity: $lockedSession,
                    entityId: $lockedSession->id,
                    oldValues: null,
                    newValues: ['changes' => $auditChanges],
                    actorId: $teacher->id
                );
            }
        });
    }

    /**
     * Close an attendance session.
     */
    public function closeSession(AttendanceSession $session, User $teacher): AttendanceSession
    {
        return DB::transaction(function () use ($session, $teacher) {
            /** @var AttendanceSession $lockedSession */
            $lockedSession = AttendanceSession::where('id', $session->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedSession->isOpen()) {
                throw ValidationException::withMessages([
                    'session' => 'Only open sessions can be closed.',
                ]);
            }

            $lockedSession->update([
                'status' => AttendanceSession::STATUS_CLOSED,
                'closed_at' => now(),
            ]);

            $this->auditLogService->log(
                action: 'close_attendance_session',
                entity: $lockedSession,
                entityId: $lockedSession->id,
                oldValues: ['status' => AttendanceSession::STATUS_OPEN, 'closed_at' => null],
                newValues: ['status' => AttendanceSession::STATUS_CLOSED, 'closed_at' => $lockedSession->closed_at->toDateTimeString()],
                actorId: $teacher->id
            );

            return $lockedSession;
        });
    }

    /**
     * Reopen a closed session with reason logging.
     */
    public function reopenSession(AttendanceSession $session, string $reason, User $teacher): AttendanceSession
    {
        return DB::transaction(function () use ($session, $reason, $teacher) {
            /** @var AttendanceSession $lockedSession */
            $lockedSession = AttendanceSession::where('id', $session->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedSession->isClosed()) {
                throw ValidationException::withMessages([
                    'session' => 'Only closed sessions can be reopened.',
                ]);
            }

            $oldClosedAt = $lockedSession->closed_at?->toDateTimeString();

            $lockedSession->update([
                'status' => AttendanceSession::STATUS_OPEN,
                'closed_at' => null,
            ]);

            $this->auditLogService->log(
                action: 'reopen_attendance_session',
                entity: $lockedSession,
                entityId: $lockedSession->id,
                oldValues: ['status' => AttendanceSession::STATUS_CLOSED, 'closed_at' => $oldClosedAt],
                newValues: ['status' => AttendanceSession::STATUS_OPEN, 'reason' => $reason],
                actorId: $teacher->id
            );

            return $lockedSession;
        });
    }

    /**
     * Cancel an attendance session.
     */
    public function cancelSession(AttendanceSession $session, string $reason, User $teacher): AttendanceSession
    {
        return DB::transaction(function () use ($session, $reason, $teacher) {
            /** @var AttendanceSession $lockedSession */
            $lockedSession = AttendanceSession::where('id', $session->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedSession->isCancelled()) {
                throw ValidationException::withMessages([
                    'session' => 'Session is already cancelled.',
                ]);
            }

            $oldStatus = $lockedSession->status;

            $lockedSession->update([
                'status' => AttendanceSession::STATUS_CANCELLED,
                'notes' => $lockedSession->notes ? $lockedSession->notes . "\n[Cancelled: {$reason}]" : "[Cancelled: {$reason}]",
            ]);

            $this->auditLogService->log(
                action: 'cancel_attendance_session',
                entity: $lockedSession,
                entityId: $lockedSession->id,
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => AttendanceSession::STATUS_CANCELLED, 'reason' => $reason],
                actorId: $teacher->id
            );

            return $lockedSession;
        });
    }
}
