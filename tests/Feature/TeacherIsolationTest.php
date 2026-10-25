<?php

namespace Tests\Feature;

use App\Models\AttendanceSession;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected Teacher $teacherA;
    protected Teacher $teacherB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teacherA = Teacher::factory()->create(['name' => 'Teacher A']);
        $this->teacherB = Teacher::factory()->create(['name' => 'Teacher B']);
    }

    public function test_teacher_a_cannot_view_or_edit_teacher_b_class(): void
    {
        $classB = SchoolClass::factory()->create(['teacher_id' => $this->teacherB->id]);

        $this->actingAs($this->teacherA);

        // View class
        $this->get(route('classes.show', $classB))->assertForbidden();

        // Edit class
        $this->get(route('classes.edit', $classB))->assertForbidden();

        // Update class
        $this->put(route('classes.update', $classB), [
            'class_name' => 'Hacked Class',
            'subject_name' => 'Hacked Subject',
        ])->assertForbidden();

        // Delete class
        $this->delete(route('classes.destroy', $classB))->assertForbidden();
    }

    public function test_teacher_a_classes_index_does_not_list_teacher_b_classes(): void
    {
        $classA = SchoolClass::factory()->create([
            'teacher_id' => $this->teacherA->id,
            'class_name' => 'Class Alpha',
            'subject_name' => 'Alpha Subject',
        ]);
        $classB = SchoolClass::factory()->create([
            'teacher_id' => $this->teacherB->id,
            'class_name' => 'Class Beta',
            'subject_name' => 'Beta Subject',
        ]);

        $response = $this->actingAs($this->teacherA)->get(route('classes.index'));

        $response->assertOk();
        $response->assertSee('Class Alpha');
        $response->assertDontSee('Class Beta');
    }

    public function test_teacher_a_cannot_view_teacher_b_attendance_session(): void
    {
        $classB = SchoolClass::factory()->create(['teacher_id' => $this->teacherB->id]);
        $sessionB = AttendanceSession::factory()->create(['class_id' => $classB->id]);

        $this->actingAs($this->teacherA);

        $this->get(route('sessions.show', $sessionB))->assertForbidden();
        $this->delete(route('sessions.destroy', $sessionB))->assertForbidden();
    }

    public function test_teacher_a_cannot_view_or_edit_teacher_b_student(): void
    {
        $classB = SchoolClass::factory()->create(['teacher_id' => $this->teacherB->id]);
        $studentB = Student::factory()->create(['class_id' => $classB->id]);

        $this->actingAs($this->teacherA);

        $this->get(route('students.show', $studentB))->assertForbidden();
        $this->get(route('students.edit', $studentB))->assertForbidden();
        $this->delete(route('students.destroy', $studentB))->assertForbidden();
    }
}
