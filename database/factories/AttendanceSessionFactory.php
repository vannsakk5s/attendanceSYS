<?php

namespace Database\Factories;

use App\Models\AttendanceSession;
use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceSession>
 */
class AttendanceSessionFactory extends Factory
{
    protected $model = AttendanceSession::class;

    public function definition(): array
    {
        return [
            'class_id' => SchoolClass::factory(),
            'session_date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'start_time' => '08:00:00',
            'end_time' => '09:30:00',
        ];
    }
}
