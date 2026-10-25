<?php

namespace Database\Factories;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceRecord>
 */
class AttendanceRecordFactory extends Factory
{
    protected $model = AttendanceRecord::class;

    public function definition(): array
    {
        return [
            'attendance_session_id' => AttendanceSession::factory(),
            'student_id' => Student::factory(),
            'status' => fake()->randomElement(['present', 'present', 'present', 'late', 'absent', 'excused']),
            'note' => fake()->optional(0.3)->sentence(),
        ];
    }
}
