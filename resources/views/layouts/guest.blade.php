<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Student Attendance Management System') }} - @yield('title', 'Teacher Sign In')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; }
    </style>
</head>
<body class="h-full antialiased text-slate-800 flex items-center justify-center p-4 selection:bg-indigo-500 selection:text-white">
    <div class="w-full max-w-md">
        <!-- Logo / Title -->
        <div class="text-center mb-8">
            <div class="mx-auto h-12 w-12 rounded-2xl bg-gradient-to-tr from-indigo-500 to-indigo-400 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">AttendanceHub</h1>
            <p class="text-xs text-indigo-300 font-medium tracking-wide uppercase mt-1">Teacher Attendance Portal</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 p-8 sm:p-10">
            @yield('content')
        </div>

        <p class="text-center text-xs text-slate-500 mt-6">
            Authorized Teacher Access Only &bull; Asia/Phnom_Penh
        </p>
    </div>
</body>
</html>
