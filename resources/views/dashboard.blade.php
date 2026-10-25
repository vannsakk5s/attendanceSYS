@extends('layouts.app')

@section('title', 'Teacher Dashboard')
@section('header', 'Welcome back, ' . $teacher->name)
@section('subtitle', 'Overview of your classes, schedules, and student attendance')

@section('actions')
    <a href="{{ route('sessions.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-sm transition shadow-indigo-500/25">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Take Attendance
    </a>
    <a href="{{ route('classes.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-sm font-semibold shadow-xs transition">
        New Class
    </a>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Stat Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Classes -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-indigo-300 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Classes</p>
                    <h3 class="text-3xl font-extrabold text-slate-900 mt-1">{{ $totalClasses }}</h3>
                </div>
                <div class="h-12 w-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-500">
                <a href="{{ route('classes.index') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold inline-flex items-center gap-1">
                    Manage classes &rarr;
                </a>
            </div>
        </div>

        <!-- Students -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-emerald-300 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Students</p>
                    <h3 class="text-3xl font-extrabold text-slate-900 mt-1">{{ $totalStudents }}</h3>
                </div>
                <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-500">
                <a href="{{ route('students.index') }}" class="text-emerald-600 hover:text-emerald-700 font-semibold inline-flex items-center gap-1">
                    View student directory &rarr;
                </a>
            </div>
        </div>

        <!-- Sessions -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-violet-300 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Recorded Sessions</p>
                    <h3 class="text-3xl font-extrabold text-slate-900 mt-1">{{ $totalSessions }}</h3>
                </div>
                <div class="h-12 w-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-500">
                <a href="{{ route('sessions.index') }}" class="text-violet-600 hover:text-violet-700 font-semibold inline-flex items-center gap-1">
                    View session history &rarr;
                </a>
            </div>
        </div>

        <!-- Attendance Rate -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-amber-300 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Attendance Rate</p>
                    <h3 class="text-3xl font-extrabold text-slate-900 mt-1">{{ $overallAttendanceRate }}%</h3>
                </div>
                <div class="h-12 w-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-500">
                <a href="{{ route('reports.index') }}" class="text-amber-600 hover:text-amber-700 font-semibold inline-flex items-center gap-1">
                    Open reports &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Today's Schedule Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-slate-900">Today's Class Schedule</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200/50">
                        {{ $todayDayOfWeek }}, {{ now()->format('M d, Y') }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Classes scheduled for teaching today</p>
            </div>
        </div>

        <div class="mt-5">
            @if($todaySchedules->isEmpty())
                <div class="text-center py-8 bg-slate-50/60 rounded-xl border border-dashed border-slate-200">
                    <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="mt-2 text-sm font-semibold text-slate-700">No classes scheduled for {{ $todayDayOfWeek }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Enjoy your day or create an unscheduled session if needed.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($todaySchedules as $sched)
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between hover:bg-slate-100/60 transition">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-indigo-100 text-indigo-700">
                                        {{ $sched->shift }}
                                    </span>
                                    <span class="text-xs font-semibold text-slate-600 font-mono">
                                        {{ \Carbon\Carbon::parse($sched->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($sched->end_time)->format('H:i') }}
                                    </span>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 mt-2">{{ $sched->schoolClass->class_name }}</h3>
                                <p class="text-xs text-slate-500">{{ $sched->schoolClass->subject_name }}</p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between">
                                <a href="{{ route('classes.show', $sched->schoolClass) }}" class="text-xs text-slate-600 hover:text-slate-900 font-medium">
                                    View Class
                                </a>
                                <a href="{{ route('sessions.create', ['class_id' => $sched->class_id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold transition">
                                    Take Attendance
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Active Classes & Recent Sessions Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Classes Overview (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">My Classes</h2>
                    <p class="text-xs text-slate-500">Your assigned courses and enrolled students</p>
                </div>
                <a href="{{ route('classes.create') }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold">
                    + Add New Class
                </a>
            </div>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($classes as $c)
                    <div class="p-4 rounded-xl border border-slate-200/80 hover:border-indigo-300 hover:shadow-xs transition bg-gradient-to-br from-white to-slate-50/50 flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-bold text-slate-900 text-base">{{ $c->class_name }}</h3>
                                    <p class="text-xs text-slate-500 font-medium">{{ $c->subject_name }}</p>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                    {{ $c->students_count }} Students
                                </span>
                            </div>

                            <!-- Schedule pills -->
                            <div class="mt-3 flex flex-wrap gap-1.5">
                                @forelse($c->schedules as $cs)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600">
                                        {{ substr($cs->day_of_week, 0, 3) }} ({{ \Carbon\Carbon::parse($cs->start_time)->format('H:i') }})
                                    </span>
                                @empty
                                    <span class="text-[11px] text-slate-400 italic">No schedule set</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs text-slate-400">{{ $c->attendance_sessions_count }} sessions</span>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('sessions.create', ['class_id' => $c->id]) }}" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold transition">
                                    Take Attendance
                                </a>
                                <a href="{{ route('classes.show', $c) }}" class="p-1 text-slate-400 hover:text-slate-700 transition" title="Class Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 text-center py-8">
                        <p class="text-sm text-slate-500">No classes created yet.</p>
                        <a href="{{ route('classes.create') }}" class="mt-2 inline-block px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold">
                            Create your first class
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Attendance Sessions (1 col) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Recent Sessions</h2>
                    <p class="text-xs text-slate-500">Latest attendance taken</p>
                </div>
                <a href="{{ route('sessions.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold">
                    View All
                </a>
            </div>

            <div class="mt-4 space-y-3">
                @forelse($recentSessions as $sess)
                    <a href="{{ route('sessions.show', $sess) }}" class="block p-3 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-slate-50/60 transition group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 group-hover:text-indigo-600 transition">
                                {{ $sess->schoolClass->class_name }}
                            </span>
                            <span class="text-[11px] font-semibold text-slate-500">
                                {{ $sess->session_date->format('M d') }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5 truncate">{{ $sess->schoolClass->subject_name }}</p>
                        
                        <div class="mt-2 flex items-center justify-between text-[11px]">
                            <div class="flex items-center gap-2">
                                <span class="text-emerald-700 font-medium">{{ $sess->present_count }} P</span>
                                <span class="text-amber-700 font-medium">{{ $sess->late_count }} L</span>
                                <span class="text-rose-700 font-medium">{{ $sess->absent_count }} A</span>
                                <span class="text-blue-700 font-medium">{{ $sess->excused_count }} E</span>
                            </div>
                            <span class="font-bold text-slate-700">{{ $sess->attendance_rate }}%</span>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-8">
                        <p class="text-xs text-slate-400">No attendance sessions recorded yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
