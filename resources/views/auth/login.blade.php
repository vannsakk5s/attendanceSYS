@extends('layouts.guest')

@section('title', 'Teacher Sign In')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-900 tracking-tight">Teacher Sign In</h2>
        <p class="text-xs text-slate-500 mt-1">Enter your credentials to access your classes and attendance rosters.</p>
    </div>

    @if (session('status'))
        <div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Teacher Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-900 placeholder:text-slate-400 transition"
                placeholder="teacher@example.com">
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Password</label>
                <a href="{{ route('password.request') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Forgot password?</a>
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-900 placeholder:text-slate-400 transition"
                placeholder="••••••••">
        </div>

        <div class="flex items-center">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                <span class="text-xs text-slate-600">Remember this device</span>
            </label>
        </div>

        <button type="submit"
            class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-500/30 text-white font-semibold rounded-xl text-sm transition cursor-pointer shadow-md shadow-indigo-600/20">
            Sign In as Teacher
        </button>

        @include('auth.social-buttons')

        <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-600">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-800 ml-1">Register</a>
        </div>
    </form>
@endsection
