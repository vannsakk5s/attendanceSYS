<?php

namespace Database\Factories;

use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolClass>
 */
class SchoolClassFactory extends Factory
{
    protected $model = SchoolClass::class;

    public function definition(): array
    {
        return [
            'teacher_id' => Teacher::factory(),
            'class_name' => fake()->randomElement(['Grade 10A', 'Grade 10B', 'Grade 11A', 'Grade 11B', 'MIS-Y2-A', 'CS-Y3-B']),
            'subject_name' => fake()->randomElement(['Mathematics', 'Database Systems', 'Java Programming', 'Web Development', 'Physics', 'English']),
        ];
    }
}
