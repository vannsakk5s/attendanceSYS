<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Student Attendance Management') }} - @yield('title', 'Teacher Portal')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full antialiased text-slate-800 bg-slate-50">
    <div class="min-h-full flex flex-col lg:flex-row">
        <!-- Desktop Sidebar -->
        <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 bg-slate-900 border-r border-slate-800 z-30 shadow-xl">
            <!-- Brand / Logo -->
            <div class="flex items-center justify-between h-16 px-6 bg-slate-950 border-b border-slate-800/80">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-indigo-400 flex items-center justify-center text-white shadow-md shadow-indigo-500/25 group-hover:scale-105 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <span class="font-bold text-white tracking-tight text-sm block">AttendPro</span>
                        <span class="text-[10px] text-indigo-400 font-semibold tracking-wider uppercase">Teacher System</span>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('classes.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('classes.*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    My Classes
                </a>

                <a href="{{ route('students.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('students.*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Students Directory
                </a>

                <a href="{{ route('sessions.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('sessions.*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Attendance Sessions
                </a>

                <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('reports.*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Attendance Reports
                </a>
            </nav>

            <!-- Bottom Teacher Profile -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/60">
                <div class="flex items-center justify-between">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 overflow-hidden group">
                        <div class="h-9 w-9 rounded-xl bg-indigo-500/20 text-indigo-400 font-bold flex items-center justify-center shrink-0 border border-indigo-500/30 group-hover:border-indigo-400 transition">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="truncate text-left">
                            <span class="block text-xs font-semibold text-white truncate group-hover:text-indigo-300 transition">{{ auth()->user()->name }}</span>
                            <span class="block text-[11px] text-slate-400 truncate">{{ auth()->user()->email }}</span>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-slate-800/80 rounded-lg transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Mobile Header -->
        <header class="lg:hidden bg-slate-900 border-b border-slate-800 px-4 py-3 flex items-center justify-between sticky top-0 z-40">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold">
                    A
                </div>
                <span class="font-bold text-white text-sm">AttendPro</span>
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('profile.edit') }}" class="text-xs text-slate-300 font-medium">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-rose-400 font-medium">Logout</button>
                </form>
            </div>
        </header>

        <!-- Mobile Bottom Nav -->
        <div class="lg:hidden fixed bottom-0 inset-x-0 bg-slate-900 border-t border-slate-800 flex justify-around py-2 z-40 text-[11px]">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center py-1 px-2 {{ request()->routeIs('dashboard') ? 'text-indigo-400 font-bold' : 'text-slate-400' }}">
                <span>Dashboard</span>
            </a>
            <a href="{{ route('classes.index') }}" class="flex flex-col items-center py-1 px-2 {{ request()->routeIs('classes.*') ? 'text-indigo-400 font-bold' : 'text-slate-400' }}">
                <span>Classes</span>
            </a>
            <a href="{{ route('students.index') }}" class="flex flex-col items-center py-1 px-2 {{ request()->routeIs('students.*') ? 'text-indigo-400 font-bold' : 'text-slate-400' }}">
                <span>Students</span>
            </a>
            <a href="{{ route('sessions.index') }}" class="flex flex-col items-center py-1 px-2 {{ request()->routeIs('sessions.*') ? 'text-indigo-400 font-bold' : 'text-slate-400' }}">
                <span>Sessions</span>
            </a>
            <a href="{{ route('reports.index') }}" class="flex flex-col items-center py-1 px-2 {{ request()->routeIs('reports.*') ? 'text-indigo-400 font-bold' : 'text-slate-400' }}">
                <span>Reports</span>
            </a>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 lg:pl-64 flex flex-col min-h-screen">
            <!-- Top Header -->
            <header class="bg-white border-b border-slate-200/80 px-6 py-4 flex items-center justify-between sticky top-0 z-20 shadow-xs">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">@yield('header', 'Dashboard')</h1>
                    @hasSection('subtitle')
                        <p class="text-xs text-slate-500 mt-0.5">@yield('subtitle')</p>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    @yield('actions')
                </div>
            </header>

            <!-- Page Body -->
            <main class="flex-1 p-6 max-w-7xl w-full mx-auto pb-20 lg:pb-10">
                <!-- Flash Notification Banner -->
                @if (session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-xs">
                        <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm font-medium flex-1">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if (session('error') || $errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-xs">
                        <svg class="w-5 h-5 text-rose-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm flex-1">
                            @if (session('error'))
                                <p class="font-medium">{{ session('error') }}</p>
                            @endif
                            @if ($errors->any())
                                <ul class="list-disc list-inside space-y-1 mt-1 text-xs">
                                    @foreach ($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        window.copyTextToClipboard = async function(text) {
            if (!text) return false;
            if (navigator.clipboard && window.isSecureContext) {
                try {
                    await navigator.clipboard.writeText(text);
                    return true;
                } catch (e) {
                    console.warn('navigator.clipboard failed, falling back:', e);
                }
            }
            try {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                const successful = document.execCommand('copy');
                document.body.removeChild(textArea);
                return successful;
            } catch (err) {
                console.error('Fallback clipboard copy failed:', err);
                return false;
            }
        };
    </script>
</body>
</html>
