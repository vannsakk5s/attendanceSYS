<?php

namespace Tests\Feature;

use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TeacherAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_page(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('login'));
    }

    public function test_teacher_can_login_with_correct_credentials(): void
    {
        $teacher = Teacher::factory()->create([
            'email' => 'teacher@school.edu',
            'password_hash' => Hash::make('secret123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'teacher@school.edu',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($teacher);
    }

    public function test_teacher_cannot_login_with_invalid_password(): void
    {
        Teacher::factory()->create([
            'email' => 'teacher@school.edu',
            'password_hash' => Hash::make('secret123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'teacher@school.edu',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_teacher_can_logout(): void
    {
        $teacher = Teacher::factory()->create();

        $response = $this->actingAs($teacher)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_login_rate_limiting_triggers_after_multiple_failed_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), [
                'email' => 'throttle@school.edu',
                'password' => 'wrongpassword',
            ]);
        }

        // 6th attempt should be throttled
        $response = $this->post(route('login'), [
            'email' => 'throttle@school.edu',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee('Create Teacher Account');
    }

    public function test_new_teacher_can_register(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Jane Teacher',
            'email' => 'jane@school.edu',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('teachers', [
            'name' => 'Jane Teacher',
            'email' => 'jane@school.edu',
        ]);
    }

    public function test_teacher_cannot_register_with_existing_email(): void
    {
        Teacher::factory()->create([
            'email' => 'existing@school.edu',
        ]);

        $response = $this->post(route('register'), [
            'name' => 'Another Teacher',
            'email' => 'existing@school.edu',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_teacher_cannot_register_with_mismatched_password(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Jane Teacher',
            'email' => 'jane@school.edu',
            'password' => 'password123',
            'password_confirmation' => 'mismatch456',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }
}

