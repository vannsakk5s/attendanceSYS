<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceReportTest extends TestCase
{
    use RefreshDatabase;

    protected Teacher $teacher;
    protected SchoolClass $class;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teacher = Teacher::factory()->create();
        $this->class = SchoolClass::factory()->create([
            'teacher_id' => $this->teacher->id,
            'class_name' => 'Grade 10A',
            'subject_name' => 'Mathematics',
        ]);

        $this->student = Student::factory()->create([
            'class_id' => $this->class->id,
            'student_code' => 'STU-001',
            'full_name' => 'Alice Test',
        ]);
    }

    public function test_report_index_shows_teacher_classes(): void
    {
        $session = AttendanceSession::factory()->create([
            'class_id' => $this->class->id,
            'session_date' => '2026-08-10',
            'start_time' => '08:00:00',
            'end_time' => '09:30:00',
        ]);

        AttendanceRecord::create([
            'attendance_session_id' => $session->id,
            'student_id' => $this->student->id,
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->teacher)->get(route('reports.index'));

        $response->assertOk();
        $response->assertSee('Grade 10A');
        $response->assertSee('Mathematics');
    }

    public function test_class_report_matrix_view(): void
    {
        $session = AttendanceSession::factory()->create([
            'class_id' => $this->class->id,
            'session_date' => '2026-08-10',
            'start_time' => '08:00:00',
            'end_time' => '09:30:00',
        ]);

        AttendanceRecord::create([
            'attendance_session_id' => $session->id,
            'student_id' => $this->student->id,
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->teacher)->get(route('reports.show', $this->class));

        $response->assertOk();
        $response->assertSee('Alice Test');
        $response->assertSee('STU-001');
        $response->assertSee('100%');
    }

    public function test_export_csv_returns_csv_file(): void
    {
        $session = AttendanceSession::factory()->create([
            'class_id' => $this->class->id,
        ]);

        AttendanceRecord::create([
            'attendance_session_id' => $session->id,
            'student_id' => $this->student->id,
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->teacher)->get(route('reports.export', $this->class));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('Alice Test', $response->getContent());
        $this->assertStringContainsString('STU-001', $response->getContent());
    }

    public function test_print_view_renders_cleanly(): void
    {
        $session = AttendanceSession::factory()->create(['class_id' => $this->class->id]);
        AttendanceRecord::create([
            'attendance_session_id' => $session->id,
            'student_id' => $this->student->id,
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->teacher)->get(route('reports.print', $this->class));

        $response->assertOk();
        $response->assertSee('OFFICIAL ATTENDANCE REPORT');
        $response->assertSee('Alice Test');
    }
}
