<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PublicAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_class_attendance_using_valid_share_token(): void
    {
        $teacher = Teacher::factory()->create();
        $class = SchoolClass::factory()->create([
            'teacher_id' => $teacher->id,
            'class_name' => 'Grade 10 - Physics',
            'subject_name' => 'Physics',
            'share_token' => Str::random(32),
        ]);

        $student = Student::factory()->create([
            'class_id' => $class->id,
            'student_code' => 'STU-101',
            'full_name' => 'Sarah Connor',
        ]);

        $session = AttendanceSession::factory()->create([
            'class_id' => $class->id,
            'session_date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:30:00',
        ]);

        AttendanceRecord::factory()->create([
            'attendance_session_id' => $session->id,
            'student_id' => $student->id,
            'status' => 'present',
        ]);

        $response = $this->get(route('public.class.attendance', $class->share_token));

        $response->assertStatus(200);
        $response->assertSee('Grade 10 - Physics');
        $response->assertSee('Physics');
        $response->assertSee('Sarah Connor');
        $response->assertSee('STU-101');
    }

    public function test_invalid_share_token_returns_404(): void
    {
        $response = $this->get('/attendance/class/invalid-token-12345');
        $response->assertStatus(404);
    }

    public function test_student_code_search_filters_individual_student(): void
    {
        $teacher = Teacher::factory()->create();
        $class = SchoolClass::factory()->create([
            'teacher_id' => $teacher->id,
            'share_token' => Str::random(32),
        ]);

        $student = Student::factory()->create([
            'class_id' => $class->id,
            'student_code' => 'STU-999',
            'full_name' => 'John Wick',
        ]);

        $response = $this->get(route('public.class.attendance', [
            'token' => $class->share_token,
            'student_code' => 'STU-999',
        ]));

        $response->assertStatus(200);
        $response->assertSee('John Wick');
        $response->assertSee('STU-999');
    }

    public function test_teacher_can_regenerate_share_token(): void
    {
        $teacher = Teacher::factory()->create();
        $oldToken = Str::random(32);
        $class = SchoolClass::factory()->create([
            'teacher_id' => $teacher->id,
            'share_token' => $oldToken,
        ]);

        $response = $this->actingAs($teacher)
            ->post(route('classes.share-link.regenerate', $class));

        $response->assertRedirect();
        $class->refresh();

        $this->assertNotEmpty($class->share_token);
        $this->assertNotEquals($oldToken, $class->share_token);
    }

    public function test_unauthorized_teacher_cannot_regenerate_another_teachers_share_token(): void
    {
        $teacher1 = Teacher::factory()->create();
        $teacher2 = Teacher::factory()->create();

        $originalToken = Str::random(32);
        $class = SchoolClass::factory()->create([
            'teacher_id' => $teacher1->id,
            'share_token' => $originalToken,
        ]);

        $response = $this->actingAs($teacher2)
            ->post(route('classes.share-link.regenerate', $class));

        $response->assertStatus(403);
        $class->refresh();
        $this->assertEquals($originalToken, $class->share_token);
    }

    public function test_guest_can_view_session_attendance_using_valid_share_token(): void
    {
        $teacher = Teacher::factory()->create();
        $class = SchoolClass::factory()->create([
            'teacher_id' => $teacher->id,
            'class_name' => 'Grade 11 - Computer Science',
            'subject_name' => 'Algorithms',
        ]);

        $student = Student::factory()->create([
            'class_id' => $class->id,
            'student_code' => 'STU-CS101',
            'full_name' => 'Ada Lovelace',
        ]);

        $session = AttendanceSession::factory()->create([
            'class_id' => $class->id,
            'session_date' => now()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:30:00',
            'share_token' => Str::random(32),
        ]);

        AttendanceRecord::factory()->create([
            'attendance_session_id' => $session->id,
            'student_id' => $student->id,
            'status' => 'present',
            'note' => 'Perfect attendance',
        ]);

        $response = $this->get(route('public.session.attendance', $session->share_token));

        $response->assertStatus(200);
        $response->assertSee('Grade 11 - Computer Science');
        $response->assertSee('Algorithms');
        $response->assertSee('Ada Lovelace');
        $response->assertSee('STU-CS101');
        $response->assertSee('Perfect attendance');
    }

    public function test_invalid_session_share_token_returns_404(): void
    {
        $response = $this->get('/attendance/session/invalid-token-xyz');
        $response->assertStatus(404);
    }

    public function test_teacher_can_regenerate_session_share_token(): void
    {
        $teacher = Teacher::factory()->create();
        $class = SchoolClass::factory()->create([
            'teacher_id' => $teacher->id,
        ]);
        $oldToken = Str::random(32);
        $session = AttendanceSession::factory()->create([
            'class_id' => $class->id,
            'share_token' => $oldToken,
        ]);

        $response = $this->actingAs($teacher)
            ->post(route('sessions.share-link.regenerate', $session));

        $response->assertRedirect();
        $session->refresh();

        $this->assertNotEmpty($session->share_token);
        $this->assertNotEquals($oldToken, $session->share_token);
    }
}

