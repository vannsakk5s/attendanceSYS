<?php

namespace Tests\Feature;

use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class SocialAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_unsupported_provider_redirect_returns_error(): void
    {
        $response = $this->get(route('social.redirect', ['provider' => 'twitter']));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }

    public function test_google_login_creates_new_teacher_and_authenticates(): void
    {
        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('google-123456');
        $abstractUser->shouldReceive('getName')->andReturn('Google Teacher');
        $abstractUser->shouldReceive('getNickname')->andReturn(null);
        $abstractUser->shouldReceive('getEmail')->andReturn('googleteacher@school.edu');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('social.callback', ['provider' => 'google']));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('teachers', [
            'name' => 'Google Teacher',
            'email' => 'googleteacher@school.edu',
            'provider' => 'google',
            'provider_id' => 'google-123456',
        ]);
    }

    public function test_github_login_links_existing_teacher_by_email(): void
    {
        $existingTeacher = Teacher::factory()->create([
            'email' => 'githubdev@school.edu',
            'provider' => null,
            'provider_id' => null,
        ]);

        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('github-99999');
        $abstractUser->shouldReceive('getName')->andReturn('GitHub Developer');
        $abstractUser->shouldReceive('getNickname')->andReturn('gitdev');
        $abstractUser->shouldReceive('getEmail')->andReturn('githubdev@school.edu');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://avatars.githubusercontent.com/u/99999');

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('github')->andReturn($provider);

        $response = $this->get(route('social.callback', ['provider' => 'github']));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($existingTeacher);

        $this->assertDatabaseHas('teachers', [
            'id' => $existingTeacher->id,
            'email' => 'githubdev@school.edu',
            'provider' => 'github',
            'provider_id' => 'github-99999',
        ]);
    }

    public function test_telegram_callback_verifies_valid_signature(): void
    {
        config(['services.telegram.bot_token' => '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11']);

        $authData = [
            'auth_date' => time(),
            'first_name' => 'Telegram',
            'id' => '11223344',
            'last_name' => 'Teacher',
            'photo_url' => 'https://t.me/i/userpic/320/photo.jpg',
            'username' => 'telegramteacher',
        ];

        $checkList = [];
        foreach ($authData as $k => $v) {
            $checkList[] = "$k=$v";
        }
        sort($checkList);
        $dataCheckString = implode("\n", $checkList);
        $secretKey = hash('sha256', '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11', true);
        $hash = hash_hmac('sha256', $dataCheckString, $secretKey);

        $params = array_merge($authData, ['hash' => $hash]);

        $response = $this->get(route('social.telegram.callback', $params));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('teachers', [
            'provider' => 'telegram',
            'provider_id' => '11223344',
            'telegram_username' => 'telegramteacher',
        ]);
    }

    public function test_telegram_callback_rejects_invalid_signature(): void
    {
        config(['services.telegram.bot_token' => '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11']);

        $params = [
            'id' => '11223344',
            'first_name' => 'Fake',
            'auth_date' => time(),
            'hash' => 'invalidhash123456',
        ];

        $response = $this->get(route('social.telegram.callback', $params));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
