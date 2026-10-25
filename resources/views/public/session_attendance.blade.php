<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $class->class_name }} - Session Attendance ({{ $session->session_date->format('M d, Y') }})</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-full antialiased text-slate-800 bg-slate-50">
    <div class="min-h-screen flex flex-col">
        <!-- Top Public Navbar -->
        <header class="bg-slate-900 border-b border-slate-800 text-white sticky top-0 z-30 shadow-md">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-indigo-400 flex items-center justify-center text-white font-bold shadow-md shadow-indigo-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <span class="font-bold text-white tracking-tight text-sm block">AttendPro</span>
                        <span class="text-[10px] text-indigo-400 font-semibold tracking-wider uppercase">Student Attendance Portal</span>
                    </div>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Live Session Record
                    </span>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 py-8 space-y-6"
            x-data="{
                search: '',
                statusFilter: 'all',
                isMatch(code, name, status) {
                    const q = this.search.toLowerCase().trim();
                    const matchesSearch = !q || code.toLowerCase().includes(q) || name.toLowerCase().includes(q);
                    const matchesStatus = this.statusFilter === 'all' || status === this.statusFilter;
                    return matchesSearch && matchesStatus;
                }
            }">

            <!-- Hero Session Header Card -->
            <div class="bg-gradient-to-br from-indigo-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-indigo-500/30 text-indigo-200 border border-indigo-400/20">
                                Session Attendance
                            </span>
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-white/10 text-white">
                                {{ $session->session_date->format('l, F d, Y') }}
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                            {{ $class->class_name }}
                        </h1>
                        <p class="text-base text-indigo-200 font-semibold mt-1">
                            {{ $class->subject_name }}
                        </p>
                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-300 mt-3">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Time: <strong class="text-white font-mono">{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</strong>
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Teacher: <strong class="text-white">{{ $class->teacher->name }}</strong>
                            </span>
                        </div>
                    </div>

                    <!-- Session Stats -->
                    <div class="flex flex-col sm:items-end gap-3">
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10 text-center sm:text-right min-w-[180px]">
                            <span class="block text-3xl font-black text-emerald-400">{{ $session->attendance_rate }}%</span>
                            <span class="text-xs text-slate-300 font-medium">Session Attendance Rate</span>
                        </div>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-500/30">
                                {{ $session->present_count }} Present
                            </span>
                            <span class="px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/30">
                                {{ $session->late_count }} Late
                            </span>
                            <span class="px-2.5 py-1 rounded-lg bg-rose-500/20 text-rose-300 text-xs font-bold border border-rose-500/30">
                                {{ $session->absent_count }} Absent
                            </span>
                            <span class="px-2.5 py-1 rounded-lg bg-blue-500/20 text-blue-300 text-xs font-bold border border-blue-500/30">
                                {{ $session->excused_count }} Excused
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5">
                <div class="flex flex-col sm:flex-row gap-3 items-stretch justify-between">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" x-model="search"
                            placeholder="Find your name or student code (e.g. STU-00101)..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Filter by status -->
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                        <button type="button" @click="statusFilter = 'all'"
                            :class="statusFilter === 'all' ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                            class="px-3 py-2 rounded-xl text-xs font-medium transition cursor-pointer whitespace-nowrap">
                            All ({{ $records->count() }})
                        </button>
                        <button type="button" @click="statusFilter = 'present'"
                            :class="statusFilter === 'present' ? 'bg-emerald-600 text-white font-bold' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-800'"
                            class="px-3 py-2 rounded-xl text-xs font-medium transition cursor-pointer whitespace-nowrap">
                            Present ({{ $session->present_count }})
                        </button>
                        <button type="button" @click="statusFilter = 'late'"
                            :class="statusFilter === 'late' ? 'bg-amber-600 text-white font-bold' : 'bg-amber-50 hover:bg-amber-100 text-amber-800'"
                            class="px-3 py-2 rounded-xl text-xs font-medium transition cursor-pointer whitespace-nowrap">
                            Late ({{ $session->late_count }})
                        </button>
                        <button type="button" @click="statusFilter = 'absent'"
                            :class="statusFilter === 'absent' ? 'bg-rose-600 text-white font-bold' : 'bg-rose-50 hover:bg-rose-100 text-rose-800'"
                            class="px-3 py-2 rounded-xl text-xs font-medium transition cursor-pointer whitespace-nowrap">
                            Absent ({{ $session->absent_count }})
                        </button>
                        <button type="button" @click="statusFilter = 'excused'"
                            :class="statusFilter === 'excused' ? 'bg-blue-600 text-white font-bold' : 'bg-blue-50 hover:bg-blue-100 text-blue-800'"
                            class="px-3 py-2 rounded-xl text-xs font-medium transition cursor-pointer whitespace-nowrap">
                            Excused ({{ $session->excused_count }})
                        </button>
                    </div>
                </div>
            </div>

            <!-- Student Records List -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Attendance Roster</h2>
                        <p class="text-xs text-slate-500">Student status recorded for this session</p>
                    </div>
                    <span class="text-xs text-slate-500 font-medium">
                        Total <strong>{{ $records->count() }}</strong> students
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200/80">
                            <tr>
                                <th class="px-6 py-3.5">Student Code</th>
                                <th class="px-6 py-3.5">Full Name</th>
                                <th class="px-6 py-3.5">Attendance Status</th>
                                <th class="px-6 py-3.5">Teacher Note</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($records as $record)
                                <tr class="hover:bg-slate-50/70 transition"
                                    x-show="isMatch('{{ $record->student->student_code }}', '{{ addslashes($record->student->full_name) }}', '{{ $record->status }}')">
                                    <td class="px-6 py-3.5 font-mono text-xs font-bold text-slate-900">
                                        {{ $record->student->student_code }}
                                    </td>
                                    <td class="px-6 py-3.5 font-semibold text-slate-800 text-sm">
                                        {{ $record->student->full_name }}
                                    </td>
                                    <td class="px-6 py-3.5">
                                        @if($record->status === 'present')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Present
                                            </span>
                                        @elseif($record->status === 'late')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Late
                                            </span>
                                        @elseif($record->status === 'absent')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Absent
                                            </span>
                                        @elseif($record->status === 'excused')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Excused
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5 text-xs text-slate-500">
                                        @if($record->note)
                                            <span class="text-slate-700 font-medium">{{ $record->note }}</span>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                        <p class="text-sm font-medium">No student attendance records found for this session.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="mt-auto border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-400">
            <p>AttendPro Attendance Portal &bull; Live Student View</p>
        </footer>
    </div>
</body>
</html>
