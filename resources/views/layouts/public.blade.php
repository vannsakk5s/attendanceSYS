<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="referrer" content="no-referrer">

    <title>Student Attendance Record</title>

    @vite(['resources/css/app.css'])
</head>
<body class="h-full antialiased text-slate-800 bg-slate-100 flex flex-col justify-between">
    <header class="bg-white border-b border-slate-200 py-4 px-6 shadow-sm">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-sm font-bold text-slate-900 block">Student Attendance Record</span>
                    <span class="text-[11px] text-slate-500">Official Verification View</span>
                </div>
            </div>
            <span class="text-xs bg-slate-100 text-slate-600 px-3 py-1 rounded-full font-medium border border-slate-200">
                Private Link
            </span>
        </div>
    </header>

    <main class="max-w-4xl w-full mx-auto p-4 sm:p-6 my-auto">
        @yield('content')
    </main>

    <footer class="py-6 text-center text-xs text-slate-400 border-t border-slate-200/80 bg-white">
        This private link was shared by your class teacher. Scoped strictly to your enrollment.
    </footer>
</body>
</html>
