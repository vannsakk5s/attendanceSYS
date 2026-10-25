<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\Teacher;

class StudentPolicy
{
    public function viewAny(Teacher $teacher): bool
    {
        return true;
    }

    public function view(Teacher $teacher, Student $student): bool
    {
        return $student->schoolClass && $student->schoolClass->teacher_id === $teacher->id;
    }

    public function create(Teacher $teacher): bool
    {
        return true;
    }

    public function update(Teacher $teacher, Student $student): bool
    {
        return $student->schoolClass && $student->schoolClass->teacher_id === $teacher->id;
    }

    public function delete(Teacher $teacher, Student $student): bool
    {
        return $student->schoolClass && $student->schoolClass->teacher_id === $teacher->id;
    }
}
