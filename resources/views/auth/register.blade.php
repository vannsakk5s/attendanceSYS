@extends('layouts.guest')

@section('title', 'Teacher Registration')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-900 tracking-tight">Create Teacher Account</h2>
        <p class="text-xs text-slate-500 mt-1">Register to start managing your classes, students, and attendance records.</p>
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

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Full Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-900 placeholder:text-slate-400 transition"
                placeholder="e.g. Dr. John Doe">
        </div>

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Teacher Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-900 placeholder:text-slate-400 transition"
                placeholder="teacher@school.edu">
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-900 placeholder:text-slate-400 transition"
                placeholder="Minimum 8 characters">
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 text-sm text-slate-900 placeholder:text-slate-400 transition"
                placeholder="Repeat password">
        </div>

        <button type="submit"
            class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-500/30 text-white font-semibold rounded-xl text-sm transition cursor-pointer shadow-md shadow-indigo-600/20 mt-2">
            Register Account
        </button>

        @include('auth.social-buttons')

        <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-600">
            Already have an account?
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-800 ml-1">Sign In</a>
        </div>
    </form>
@endsection
