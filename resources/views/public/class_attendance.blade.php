<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $class->class_name }} Attendance - {{ $class->subject_name }}</title>

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
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Live Public Link
                    </span>
                </div>
            </div>
        </header>

        <!-- Main Container -->
        <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 py-8 space-y-6"
            x-data="{
                activeTab: '{{ $activeStudent ? 'individual' : 'overview' }}',
                selectedCode: '{{ $activeStudent ? $activeStudent['student']->student_code : '' }}',
                searchQuery: '{{ $searchCode ?? '' }}',
                students: {{ Js::from($studentsData) }},

                get currentStudent() {
                    if (!this.selectedCode) return null;
                    return this.students.find(s => s.student.student_code.toLowerCase() === this.selectedCode.toLowerCase()) || null;
                },

                selectStudent(code) {
                    this.selectedCode = code;
                    this.activeTab = 'individual';
                    window.scrollTo({ top: document.getElementById('student_card')?.offsetTop - 80 || 0, behavior: 'smooth' });
                }
            }">

            <!-- Hero Class Header Card -->
            <div class="bg-gradient-to-br from-indigo-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-indigo-500/30 text-indigo-200 border border-indigo-400/20">
                                Class Attendance Board
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                            {{ $class->class_name }}
                        </h1>
                        <p class="text-base text-indigo-200 font-semibold mt-1">
                            {{ $class->subject_name }}
                        </p>
                        <p class="text-xs text-slate-300 mt-2 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Teacher: <strong class="text-white">{{ $class->teacher->name }}</strong>
                        </p>
                    </div>

                    <!-- Header Stats -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 text-center">
                            <span class="block text-2xl font-black text-white">{{ $class->students->count() }}</span>
                            <span class="text-[11px] text-slate-300 font-medium">Students</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 text-center">
                            <span class="block text-2xl font-black text-white">{{ $sessions->count() }}</span>
                            <span class="text-[11px] text-slate-300 font-medium">Sessions Held</span>
                        </div>
                        <div class="col-span-2 sm:col-span-1 p-3.5 rounded-2xl bg-white/5 border border-white/10 text-center">
                            <span class="block text-2xl font-black text-indigo-300">{{ $class->schedules->count() }}</span>
                            <span class="text-[11px] text-slate-300 font-medium">Weekly Schedules</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Search & Selection Bar -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5">
                <div class="flex flex-col sm:flex-row gap-3 items-stretch">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" x-model="searchQuery"
                            placeholder="Type your Student Code (e.g. STU-00101) or your Name..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="sm:w-64">
                        <select x-model="selectedCode" @change="activeTab = 'individual'"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Choose My Name --</option>
                            <template x-for="item in students" :key="item.student.id">
                                <option :value="item.student.student_code" x-text="item.student.student_code + ' - ' + item.student.full_name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- Instant Search Dropdown Suggestions (if typing) -->
                <div x-show="searchQuery.trim().length > 1" class="mt-3 divide-y divide-slate-100 max-h-48 overflow-y-auto border border-slate-100 rounded-xl bg-slate-50/60 p-1">
                    <template x-for="item in students.filter(s => s.student.full_name.toLowerCase().includes(searchQuery.toLowerCase()) || s.student.student_code.toLowerCase().includes(searchQuery.toLowerCase()))" :key="item.student.id">
                        <button type="button" @click="selectStudent(item.student.student_code); searchQuery = ''"
                            class="w-full px-3 py-2 text-left text-xs font-semibold flex items-center justify-between hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition cursor-pointer">
                            <span class="flex items-center gap-2">
                                <span class="font-mono text-slate-500" x-text="item.student.student_code"></span>
                                <span class="text-slate-800" x-text="item.student.full_name"></span>
                            </span>
                            <span class="font-bold text-indigo-600" x-text="item.rate + '% Attendance'"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- View Tabs Navigation -->
            <div class="flex border-b border-slate-200 gap-2">
                <button @click="activeTab = 'individual'"
                    :class="activeTab === 'individual' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'"
                    class="py-3 px-4 border-b-2 text-sm transition cursor-pointer flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>My Personal Attendance</span>
                </button>

                <button @click="activeTab = 'overview'"
                    :class="activeTab === 'overview' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'"
                    class="py-3 px-4 border-b-2 text-sm transition cursor-pointer flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Class Attendance Overview</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                        {{ $studentsData->count() }}
                    </span>
                </button>
            </div>

            <!-- TAB 1: INDIVIDUAL STUDENT DETAIL -->
            <div x-show="activeTab === 'individual'" id="student_card" class="space-y-6">
                <template x-if="!currentStudent">
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-10 text-center shadow-xs">
                        <div class="h-16 w-16 mx-auto rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mt-4">Select or Search Your Name</h3>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Use the search box or dropdown above to view your personal attendance rate, present/absent record, and teacher notes.</p>
                    </div>
                </template>

                <template x-if="currentStudent">
                    <div class="space-y-6">
                        <!-- Student Summary Banner -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-16 rounded-2xl bg-indigo-600 text-white font-black text-2xl flex items-center justify-center shadow-md shadow-indigo-600/30 shrink-0"
                                    x-text="currentStudent.student.full_name.charAt(0)">
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h2 class="text-xl font-bold text-slate-900" x-text="currentStudent.student.full_name"></h2>
                                        <span class="px-2.5 py-0.5 rounded-md font-mono text-xs font-bold bg-slate-100 text-slate-700" x-text="currentStudent.student.student_code"></span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1">{{ $class->class_name }} • {{ $class->subject_name }}</p>
                                </div>
                            </div>

                            <!-- Attendance Gauge -->
                            <div class="flex items-center gap-4 bg-slate-50 p-3.5 rounded-2xl border border-slate-200/80">
                                <div>
                                    <span class="block text-2xl font-black"
                                        :class="currentStudent.rate >= 80 ? 'text-emerald-600' : (currentStudent.rate >= 60 ? 'text-amber-600' : 'text-rose-600')"
                                        x-text="currentStudent.rate + '%'">
                                    </span>
                                    <span class="text-[11px] text-slate-500 font-semibold">Attendance Rate</span>
                                </div>
                                <div class="h-10 w-px bg-slate-200"></div>
                                <div class="flex items-center gap-2 text-center text-xs">
                                    <div class="px-2 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-bold">
                                        <span x-text="currentStudent.present"></span> P
                                    </div>
                                    <div class="px-2 py-1 rounded-lg bg-amber-100 text-amber-800 font-bold">
                                        <span x-text="currentStudent.late"></span> L
                                    </div>
                                    <div class="px-2 py-1 rounded-lg bg-rose-100 text-rose-800 font-bold">
                                        <span x-text="currentStudent.absent"></span> A
                                    </div>
                                    <div class="px-2 py-1 rounded-lg bg-blue-100 text-blue-800 font-bold">
                                        <span x-text="currentStudent.excused"></span> E
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Individual Session History Table -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                                <h3 class="text-sm font-bold text-slate-900">Attendance Session History</h3>
                                <span class="text-xs text-slate-400 font-medium" x-text="currentStudent.records.length + ' sessions recorded'"></span>
                            </div>

                            <template x-if="currentStudent.records.length === 0">
                                <div class="p-8 text-center text-xs text-slate-400">
                                    No attendance sessions recorded yet for this student.
                                </div>
                            </template>

                            <template x-if="currentStudent.records.length > 0">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-sm">
                                        <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200/80">
                                            <tr>
                                                <th class="px-6 py-3.5">Session Date</th>
                                                <th class="px-6 py-3.5">Time</th>
                                                <th class="px-6 py-3.5">Your Attendance Status</th>
                                                <th class="px-6 py-3.5">Teacher Note</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            <template x-for="rec in currentStudent.records" :key="rec.id">
                                                <tr class="hover:bg-slate-50/70 transition">
                                                    <td class="px-6 py-3.5 font-bold text-slate-900 text-xs"
                                                        x-text="new Date(rec.attendance_session.session_date).toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' })">
                                                    </td>
                                                    <td class="px-6 py-3.5 font-mono text-slate-600 text-xs"
                                                        x-text="rec.attendance_session.start_time.substring(0,5) + ' - ' + rec.attendance_session.end_time.substring(0,5)">
                                                    </td>
                                                    <td class="px-6 py-3.5">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold"
                                                            :class="{
                                                                'bg-emerald-100 text-emerald-800': rec.status === 'present',
                                                                'bg-amber-100 text-amber-800': rec.status === 'late',
                                                                'bg-rose-100 text-rose-800': rec.status === 'absent',
                                                                'bg-blue-100 text-blue-800': rec.status === 'excused'
                                                            }"
                                                            x-text="rec.status.charAt(0).toUpperCase() + rec.status.slice(1)">
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-3.5 text-xs text-slate-600" x-text="rec.note || '—'"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- TAB 2: CLASS ATTENDANCE OVERVIEW -->
            <div x-show="activeTab === 'overview'" class="space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200/80">
                                <tr>
                                    <th class="px-5 py-3.5">#</th>
                                    <th class="px-5 py-3.5">Student Code</th>
                                    <th class="px-5 py-3.5">Full Name</th>
                                    <th class="px-4 py-3.5 text-center text-emerald-700">Present</th>
                                    <th class="px-4 py-3.5 text-center text-amber-700">Late</th>
                                    <th class="px-4 py-3.5 text-center text-rose-700">Absent</th>
                                    <th class="px-4 py-3.5 text-center text-blue-700">Excused</th>
                                    <th class="px-5 py-3.5 text-right">Attendance Rate</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="(item, index) in students" :key="item.student.id">
                                    <tr class="hover:bg-slate-50/70 transition cursor-pointer" @click="selectStudent(item.student.student_code)">
                                        <td class="px-5 py-3.5 text-xs text-slate-400 font-mono" x-text="index + 1"></td>
                                        <td class="px-5 py-3.5 text-xs font-mono font-bold text-slate-900" x-text="item.student.student_code"></td>
                                        <td class="px-5 py-3.5 font-semibold text-slate-800 text-xs" x-text="item.student.full_name"></td>
                                        <td class="px-4 py-3.5 text-center font-bold text-emerald-600 text-xs" x-text="item.present"></td>
                                        <td class="px-4 py-3.5 text-center font-bold text-amber-600 text-xs" x-text="item.late"></td>
                                        <td class="px-4 py-3.5 text-center font-bold text-rose-600 text-xs" x-text="item.absent"></td>
                                        <td class="px-4 py-3.5 text-center font-bold text-blue-600 text-xs" x-text="item.excused"></td>
                                        <td class="px-5 py-3.5 text-right font-extrabold text-xs">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold"
                                                :class="item.rate >= 80 ? 'bg-emerald-50 text-emerald-700' : (item.rate >= 60 ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700')"
                                                x-text="item.rate + '%'">
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="mt-auto py-6 border-t border-slate-200 text-center text-xs text-slate-400">
            Student Attendance Portal • Powered by AttendPro &copy; {{ date('Y') }}
        </footer>
    </div>
</body>
</html>
