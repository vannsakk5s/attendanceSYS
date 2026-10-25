<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected Teacher $teacher;
    protected SchoolClass $class;
    protected Student $student1;
    protected Student $student2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teacher = Teacher::factory()->create();
        $this->class = SchoolClass::factory()->create([
            'teacher_id' => $this->teacher->id,
            'class_name' => 'Grade 10A',
            'subject_name' => 'Mathematics',
        ]);

        $this->student1 = Student::factory()->create(['class_id' => $this->class->id]);
        $this->student2 = Student::factory()->create(['class_id' => $this->class->id]);
    }

    public function test_teacher_can_create_attendance_session(): void
    {
        $response = $this->actingAs($this->teacher)->post(route('sessions.store'), [
            'class_id' => $this->class->id,
            'session_date' => '2026-09-01',
            'start_time' => '08:00',
            'end_time' => '09:30',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendance_sessions', [
            'class_id' => $this->class->id,
        ]);

        $session = AttendanceSession::where('class_id', $this->class->id)->first();
        $this->assertEquals('2026-09-01', $session->session_date->format('Y-m-d'));

        // Automatically initializes records for all students in the class
        $this->assertDatabaseHas('attendance_records', [
            'attendance_session_id' => $session->id,
            'student_id' => $this->student1->id,
            'status' => 'present',
        ]);
        $this->assertDatabaseHas('attendance_records', [
            'attendance_session_id' => $session->id,
            'student_id' => $this->student2->id,
            'status' => 'present',
        ]);
    }

    public function test_teacher_can_mark_attendance_records_with_status_and_note(): void
    {
        $session = AttendanceSession::factory()->create([
            'class_id' => $this->class->id,
            'session_date' => '2026-09-01',
            'start_time' => '08:00:00',
            'end_time' => '09:30:00',
        ]);

        $rec1 = AttendanceRecord::create([
            'attendance_session_id' => $session->id,
            'student_id' => $this->student1->id,
            'status' => 'present',
        ]);

        $rec2 = AttendanceRecord::create([
            'attendance_session_id' => $session->id,
            'student_id' => $this->student2->id,
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->teacher)->put(route('sessions.records.update', $session), [
            'records' => [
                $rec1->id => [
                    'status' => 'present',
                    'note' => 'Active participation',
                ],
                $rec2->id => [
                    'status' => 'excused',
                    'note' => 'Doctor appointment note provided',
                ],
            ],
        ]);

        $response->assertRedirect(route('sessions.show', $session));

        $this->assertDatabaseHas('attendance_records', [
            'id' => $rec1->id,
            'status' => 'present',
            'note' => 'Active participation',
        ]);

        $this->assertDatabaseHas('attendance_records', [
            'id' => $rec2->id,
            'status' => 'excused',
            'note' => 'Doctor appointment note provided',
        ]);
    }

    public function test_teacher_can_delete_attendance_session(): void
    {
        $session = AttendanceSession::factory()->create(['class_id' => $this->class->id]);

        $response = $this->actingAs($this->teacher)->delete(route('sessions.destroy', $session));

        $response->assertRedirect(route('classes.show', [$this->class, 'tab' => 'sessions']));
        $this->assertDatabaseMissing('attendance_sessions', ['id' => $session->id]);
    }
}
