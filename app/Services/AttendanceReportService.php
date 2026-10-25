<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Student;
use App\Models\TeachingAssignment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceReportService
{
    /**
     * Calculate attendance rate:
     * Rate = (Present + Late) / (Present + Late + Absent) * 100
     * Excused is excluded from denominator.
     * Returns null if denominator is zero (displayed as "N/A").
     */
    public function calculateRate(int $present, int $late, int $absent): ?float
    {
        $denominator = $present + $late + $absent;

        if ($denominator === 0) {
            return null;
        }

        return round((($present + $late) / $denominator) * 100, 2);
    }

    /**
     * Get summary report for a Teaching Assignment.
     * Computes statistics for each student in the assignment's classroom across all closed sessions.
     */
    public function getAssignmentSummaryReport(
        TeachingAssignment $assignment,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $sessionsQuery = AttendanceSession::where('teaching_assignment_id', $assignment->id)
            ->where('status', AttendanceSession::STATUS_CLOSED)
            ->orderBy('session_date', 'asc')
            ->orderBy('start_time', 'asc');

        if ($startDate) {
            $sessionsQuery->where('session_date', '>=', $startDate);
        }
        if ($endDate) {
            $sessionsQuery->where('session_date', '<=', $endDate);
        }

        $sessions = $sessionsQuery->get();
        $sessionIds = $sessions->pluck('id');

        $students = $assignment->classroom
            ->students()
            ->orderBy('full_name')
            ->get();

        $allRecords = AttendanceRecord::whereIn('attendance_session_id', $sessionIds)
            ->get()
            ->groupBy('student_id');

        $studentsReport = [];
        $totalPresent = 0;
        $totalLate = 0;
        $totalAbsent = 0;
        $totalExcused = 0;

        foreach ($students as $student) {
            $studentRecords = $allRecords->get($student->id, collect())->keyBy('attendance_session_id');

            $present = 0;
            $late = 0;
            $absent = 0;
            $excused = 0;
            $sessionStatuses = [];

            foreach ($sessions as $session) {
                $rec = $studentRecords->get($session->id);
                $st = $rec?->status ?? 'unmarked';

                if ($st === AttendanceRecord::STATUS_PRESENT) {
                    $present++;
                } elseif ($st === AttendanceRecord::STATUS_LATE) {
                    $late++;
                } elseif ($st === AttendanceRecord::STATUS_ABSENT) {
                    $absent++;
                } elseif ($st === AttendanceRecord::STATUS_EXCUSED) {
                    $excused++;
                }

                $sessionStatuses[$session->id] = [
                    'status' => $st,
                    'remark' => $rec?->remark,
                ];
            }

            $rate = $this->calculateRate($present, $late, $absent);

            $totalPresent += $present;
            $totalLate += $late;
            $totalAbsent += $absent;
            $totalExcused += $excused;

            $studentsReport[] = [
                'student_id' => $student->id,
                'student_code' => $student->student_code,
                'full_name' => $student->full_name,
                'gender' => $student->gender,
                'phone' => $student->phone,
                'is_archived' => $student->isArchived(),
                'eligible_sessions' => $sessions->count(),
                'present' => $present,
                'late' => $late,
                'absent' => $absent,
                'excused' => $excused,
                'rate' => $rate,
                'rate_formatted' => $rate !== null ? number_format($rate, 1) . '%' : 'N/A',
                'session_statuses' => $sessionStatuses,
            ];
        }

        $overallRate = $this->calculateRate($totalPresent, $totalLate, $totalAbsent);

        return [
            'assignment' => $assignment,
            'sessions' => $sessions,
            'total_sessions' => $sessions->count(),
            'students' => $studentsReport,
            'total_students' => count($studentsReport),
            'totals' => [
                'present' => $totalPresent,
                'late' => $totalLate,
                'absent' => $totalAbsent,
                'excused' => $totalExcused,
                'overall_rate' => $overallRate,
                'overall_rate_formatted' => $overallRate !== null ? number_format($overallRate, 1) . '%' : 'N/A',
            ],
        ];
    }

    /**
     * Get attendance report for a single student.
     */
    public function getStudentReport(
        Student $student,
        ?TeachingAssignment $assignment = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $sessionsQuery = AttendanceSession::where('status', AttendanceSession::STATUS_CLOSED)
            ->whereHas('teachingAssignment', function ($q) use ($student, $assignment) {
                $q->where('classroom_id', $student->classroom_id);
                if ($assignment) {
                    $q->where('id', $assignment->id);
                }
            })
            ->orderBy('session_date', 'asc')
            ->orderBy('start_time', 'asc');

        if ($startDate) {
            $sessionsQuery->where('session_date', '>=', $startDate);
        }
        if ($endDate) {
            $sessionsQuery->where('session_date', '<=', $endDate);
        }

        $sessions = $sessionsQuery->with('teachingAssignment.subject')->get();
        $sessionIds = $sessions->pluck('id');

        $records = AttendanceRecord::where('student_id', $student->id)
            ->whereIn('attendance_session_id', $sessionIds)
            ->get()
            ->keyBy('attendance_session_id');

        $presentCount = 0;
        $lateCount = 0;
        $absentCount = 0;
        $excusedCount = 0;
        $history = [];

        foreach ($sessions as $session) {
            $record = $records->get($session->id);
            $status = $record?->status ?? 'unmarked';

            if ($status === AttendanceRecord::STATUS_PRESENT) {
                $presentCount++;
            } elseif ($status === AttendanceRecord::STATUS_LATE) {
                $lateCount++;
            } elseif ($status === AttendanceRecord::STATUS_ABSENT) {
                $absentCount++;
            } elseif ($status === AttendanceRecord::STATUS_EXCUSED) {
                $excusedCount++;
            }

            $history[] = [
                'session_id' => $session->id,
                'assignment_id' => $session->teaching_assignment_id,
                'subject_name' => $session->teachingAssignment->subject->name,
                'session_date' => $session->session_date->format('Y-m-d'),
                'start_time' => substr($session->start_time, 0, 5),
                'end_time' => substr($session->end_time, 0, 5),
                'status' => $status,
                'remark' => $record?->remark,
            ];
        }

        $rate = $this->calculateRate($presentCount, $lateCount, $absentCount);

        return [
            'student' => $student,
            'assignment' => $assignment,
            'summary' => [
                'eligible_sessions' => $sessions->count(),
                'present' => $presentCount,
                'late' => $lateCount,
                'absent' => $absentCount,
                'excused' => $excusedCount,
                'rate' => $rate,
                'rate_formatted' => $rate !== null ? number_format($rate, 1) . '%' : 'N/A',
            ],
            'history' => $history,
        ];
    }

    /**
     * Stream CSV export for a Teaching Assignment.
     */
    public function streamAssignmentCsv(
        TeachingAssignment $assignment,
        ?string $startDate = null,
        ?string $endDate = null
    ): StreamedResponse {
        $report = $this->getAssignmentSummaryReport($assignment, $startDate, $endDate);
        $sessions = $report['sessions'];
        $students = $report['students'];

        $filename = sprintf(
            'attendance_%s_%s_%s.csv',
            preg_replace('/[^A-Za-z0-9_\-]/', '_', $assignment->classroom->name),
            preg_replace('/[^A-Za-z0-9_\-]/', '_', $assignment->subject->code),
            now()->format('Ymd_His')
        );

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($report, $sessions, $students) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Metadata rows
            fputcsv($handle, ['Classroom', $report['assignment']->classroom->name]);
            fputcsv($handle, ['Subject', $report['assignment']->subject->name . ' (' . $report['assignment']->subject->code . ')']);
            fputcsv($handle, ['Teacher', $report['assignment']->teacher->name]);
            fputcsv($handle, ['Semester', $report['assignment']->semester]);
            fputcsv($handle, ['Generated At', now()->toDateTimeString()]);
            fputcsv($handle, []);

            // Dynamic header
            $headerRow = [
                'Student Code',
                'Full Name',
                'Gender',
                'Total Closed Sessions',
                'Present',
                'Late',
                'Absent',
                'Excused',
                'Attendance Rate',
            ];

            foreach ($sessions as $session) {
                $headerRow[] = $session->session_date->format('Y-m-d') . ' (' . substr($session->start_time, 0, 5) . ')';
            }

            fputcsv($handle, $headerRow);

            // Data rows
            foreach ($students as $row) {
                $dataRow = [
                    $row['student_code'],
                    $row['full_name'],
                    $row['gender'] ?? '-',
                    $row['eligible_sessions'],
                    $row['present'],
                    $row['late'],
                    $row['absent'],
                    $row['excused'],
                    $row['rate_formatted'],
                ];

                foreach ($sessions as $session) {
                    $st = $row['session_statuses'][$session->id]['status'] ?? 'unmarked';
                    $dataRow[] = ucfirst($st);
                }

                fputcsv($handle, $dataRow);
            }

            // Totals Row
            $totalsRow = [
                'TOTALS / OVERALL',
                '',
                '',
                $report['total_sessions'],
                $report['totals']['present'],
                $report['totals']['late'],
                $report['totals']['absent'],
                $report['totals']['excused'],
                $report['totals']['overall_rate_formatted'],
            ];
            fputcsv($handle, $totalsRow);

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
