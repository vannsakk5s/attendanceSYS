@extends('layouts.guest')

@section('title', 'Set New Password')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-900 tracking-tight">Set New Password</h2>
        <p class="text-xs text-slate-500 mt-1">Please enter your email and specify your new account password.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Teacher Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-900 transition">
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">New Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-900 transition"
                placeholder="••••••••">
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Confirm New Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-900 transition"
                placeholder="••••••••">
        </div>

        <button type="submit"
            class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-500/30 text-white font-semibold rounded-xl text-sm transition cursor-pointer shadow-md shadow-indigo-600/20">
            Reset Password
        </button>
    </form>
@endsection
