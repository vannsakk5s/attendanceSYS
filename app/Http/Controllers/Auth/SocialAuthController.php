<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    protected array $supportedProviders = ['google', 'github'];

    /**
     * Redirect the teacher to the OAuth provider's authentication page.
     */
    public function redirect(string $provider): RedirectResponse
    {
        if (!in_array($provider, $this->supportedProviders, true)) {
            return redirect()->route('login')->withErrors(['email' => "Authentication with {$provider} is not supported."]);
        }

        $clientId = config("services.{$provider}.client_id");
        if (empty($clientId)) {
            return redirect()->route('login')->withErrors([
                'email' => ucfirst($provider) . " authentication is not configured yet. Please set {$provider} Client ID and Secret in your .env file."
            ]);
        }

        try {
            return Socialite::driver($provider)->redirect();
        } catch (\Throwable $e) {
            Log::error("Social redirect error ({$provider}): " . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'email' => "Could not connect to {$provider}: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the callback returned by the OAuth provider.
     */
    public function callback(Request $request, string $provider): RedirectResponse
    {
        if (!in_array($provider, $this->supportedProviders, true)) {
            return redirect()->route('login')->withErrors(['email' => "Authentication with {$provider} is not supported."]);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            Log::error("Social login callback error ({$provider}): " . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'email' => "Unable to authenticate with {$provider}. Please try again."
            ]);
        }

        $email = $socialUser->getEmail();
        $providerId = (string) $socialUser->getId();
        $name = $socialUser->getName() ?: $socialUser->getNickname() ?: 'Teacher';
        $avatar = $socialUser->getAvatar();

        if (empty($email)) {
            // Fallback email for providers that don't return public email
            $email = "{$provider}_{$providerId}@users.attendancehub.local";
        }

        // Find teacher by provider + provider_id, or by email
        $teacher = Teacher::where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();

        if (!$teacher) {
            $teacher = Teacher::where('email', $email)->first();
        }

        if ($teacher) {
            // Link provider and update avatar
            $teacher->update([
                'provider' => $provider,
                'provider_id' => $providerId,
                'avatar' => $avatar ?: $teacher->avatar,
            ]);
        } else {
            // Create a new teacher
            $teacher = Teacher::create([
                'name' => $name,
                'email' => $email,
                'provider' => $provider,
                'provider_id' => $providerId,
                'avatar' => $avatar,
            ]);
        }

        Auth::login($teacher, true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('success', "Welcome, {$teacher->name}! You have successfully signed in with " . ucfirst($provider) . '.');
    }

    /**
     * Handle Telegram Login Widget callback verification.
     */
    public function telegramCallback(Request $request): RedirectResponse
    {
        $botToken = config('services.telegram.bot_token');
        if (empty($botToken)) {
            return redirect()->route('login')->withErrors([
                'email' => 'Telegram authentication is not configured yet. Please set TELEGRAM_BOT_TOKEN in .env.'
            ]);
        }

        $authData = $request->only(['id', 'first_name', 'last_name', 'username', 'photo_url', 'auth_date']);
        $hash = $request->get('hash');

        if (empty($hash) || empty($authData['id'])) {
            return redirect()->route('login')->withErrors(['email' => 'Invalid Telegram authentication payload.']);
        }

        // Validate hash using SHA256 HMAC
        $checkList = [];
        foreach ($authData as $key => $val) {
            if ($val !== null && $val !== '') {
                $checkList[] = "{$key}={$val}";
            }
        }
        sort($checkList);
        $dataCheckString = implode("\n", $checkList);
        $secretKey = hash('sha256', $botToken, true);
        $calculatedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

        if (!hash_equals($calculatedHash, (string) $hash)) {
            return redirect()->route('login')->withErrors(['email' => 'Telegram signature verification failed.']);
        }

        // Check expiration (24h)
        if (time() - (int)$authData['auth_date'] > 86400) {
            return redirect()->route('login')->withErrors(['email' => 'Telegram login session expired. Please retry.']);
        }

        $telegramId = (string) $authData['id'];
        $fullName = trim(($authData['first_name'] ?? '') . ' ' . ($authData['last_name'] ?? ''));
        $username = $authData['username'] ?? null;
        $photoUrl = $authData['photo_url'] ?? null;

        $teacher = Teacher::where('provider', 'telegram')
            ->where('provider_id', $telegramId)
            ->first();

        if (!$teacher && $username) {
            $teacher = Teacher::where('telegram_username', $username)->first();
        }

        if (!$teacher) {
            $email = $username ? "{$username}@telegram.local" : "telegram_{$telegramId}@telegram.local";
            $teacher = Teacher::create([
                'name' => $fullName ?: ($username ?: 'Telegram User'),
                'email' => $email,
                'provider' => 'telegram',
                'provider_id' => $telegramId,
                'avatar' => $photoUrl,
                'telegram_username' => $username,
            ]);
        } else {
            $teacher->update([
                'provider' => 'telegram',
                'provider_id' => $telegramId,
                'avatar' => $photoUrl ?: $teacher->avatar,
                'telegram_username' => $username ?: $teacher->telegram_username,
            ]);
        }

        Auth::login($teacher, true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('success', "Welcome, {$teacher->name}! Signed in with Telegram.");
    }
}
