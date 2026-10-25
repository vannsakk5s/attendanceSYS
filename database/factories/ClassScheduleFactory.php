<?php

namespace Database\Factories;

use App\Models\ClassSchedule;
use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassSchedule>
 */
class ClassScheduleFactory extends Factory
{
    protected $model = ClassSchedule::class;

    public function definition(): array
    {
        return [
            'class_id' => SchoolClass::factory(),
            'day_of_week' => fake()->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']),
            'shift' => fake()->randomElement(['Morning', 'Afternoon', 'Evening']),
            'start_time' => '08:00:00',
            'end_time' => '09:30:00',
        ];
    }
}
