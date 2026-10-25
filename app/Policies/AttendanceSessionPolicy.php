<?php

namespace App\Policies;

use App\Models\AttendanceSession;
use App\Models\Teacher;

class AttendanceSessionPolicy
{
    public function viewAny(Teacher $teacher): bool
    {
        return true;
    }

    public function view(Teacher $teacher, AttendanceSession $session): bool
    {
        return $session->schoolClass && $session->schoolClass->teacher_id === $teacher->id;
    }

    public function create(Teacher $teacher): bool
    {
        return true;
    }

    public function update(Teacher $teacher, AttendanceSession $session): bool
    {
        return $session->schoolClass && $session->schoolClass->teacher_id === $teacher->id;
    }

    public function delete(Teacher $teacher, AttendanceSession $session): bool
    {
        return $session->schoolClass && $session->schoolClass->teacher_id === $teacher->id;
    }
}
