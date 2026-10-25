@extends('layouts.app')

@section('title', 'Teacher Profile')
@section('header', 'Teacher Profile & Security')
@section('subtitle', 'Manage your account details and password.')

@section('content')
<div class="max-w-3xl space-y-6">
    @if (session('status') === 'profile-updated')
        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
            Profile details updated successfully.
        </div>
    @elseif (session('status') === 'password-updated')
        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
            Password changed successfully.
        </div>
    @endif

    <!-- Profile Information Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-base font-bold text-slate-900 mb-1">Profile Information</h2>
        <p class="text-xs text-slate-500 mb-5">Update your account name and email address.</p>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Full Name</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full max-w-md px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-900 transition">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Teacher Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full max-w-md px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-900 transition">
            </div>

            <div>
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-xs transition cursor-pointer shadow-sm">
                    Save Profile
                </button>
            </div>
        </form>
    </div>

    <!-- Password Change Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-base font-bold text-slate-900 mb-1">Change Password</h2>
        <p class="text-xs text-slate-500 mb-5">Ensure your account is using a secure, long password.</p>

        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Current Password</label>
                <input id="current_password" type="password" name="current_password" required autocomplete="current-password"
                    class="w-full max-w-md px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-900 transition">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">New Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="w-full max-w-md px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-900 transition">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Confirm New Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="w-full max-w-md px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-900 transition">
            </div>

            <div>
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-xs transition cursor-pointer shadow-sm">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
