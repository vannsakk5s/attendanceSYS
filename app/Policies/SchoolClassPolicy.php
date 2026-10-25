<?php

namespace App\Policies;

use App\Models\SchoolClass;
use App\Models\Teacher;

class SchoolClassPolicy
{
    public function viewAny(Teacher $teacher): bool
    {
        return true;
    }

    public function view(Teacher $teacher, SchoolClass $schoolClass): bool
    {
        return $schoolClass->teacher_id === $teacher->id;
    }

    public function create(Teacher $teacher): bool
    {
        return true;
    }

    public function update(Teacher $teacher, SchoolClass $schoolClass): bool
    {
        return $schoolClass->teacher_id === $teacher->id;
    }

    public function delete(Teacher $teacher, SchoolClass $schoolClass): bool
    {
        return $schoolClass->teacher_id === $teacher->id;
    }
}
