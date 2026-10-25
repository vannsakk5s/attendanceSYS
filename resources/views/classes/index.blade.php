@extends('layouts.app')

@section('title', 'My Classes')
@section('header', 'My Classes')
@section('subtitle', 'Manage your classes, subjects, schedules, and student rosters')

@section('actions')
    <a href="{{ route('classes.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-sm transition shadow-indigo-500/25">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Class
    </a>
@endsection

@section('content')
<div class="space-y-6">
    @if($classes->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-xs">
            <div class="h-16 w-16 mx-auto rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 mt-4">No Classes Found</h3>
            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Get started by creating your first class with its subject name.</p>
            <div class="mt-6">
                <a href="{{ route('classes.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition">
                    + Create First Class
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($classes as $class)
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-indigo-300 hover:shadow-md transition flex flex-col justify-between overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 tracking-tight">{{ $class->class_name }}</h3>
                                <p class="text-sm text-indigo-600 font-medium mt-0.5">{{ $class->subject_name }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 shrink-0">
                                {{ $class->students_count }} Students
                            </span>
                        </div>

                        <!-- Schedules List -->
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Weekly Schedule</p>
                            @if($class->schedules->isEmpty())
                                <p class="text-xs text-slate-400 italic">No schedules added yet</p>
                            @else
                                <div class="space-y-1.5">
                                    @foreach($class->schedules as $sched)
                                        <div class="flex items-center justify-between text-xs py-1 px-2 rounded-lg bg-slate-50 text-slate-600">
                                            <span class="font-semibold text-slate-700">{{ $sched->day_of_week }} ({{ $sched->shift }})</span>
                                            <span class="font-mono text-slate-500">{{ \Carbon\Carbon::parse($sched->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($sched->end_time)->format('H:i') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Attendance Summary -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <span>{{ $class->attendance_sessions_count }} Attendance Sessions</span>
                            <a href="{{ route('reports.show', $class) }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">
                                View Report &rarr;
                            </a>
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="px-6 py-3.5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('classes.show', $class) }}" class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-lg shadow-xs transition">
                                Manage
                            </a>
                            <a href="{{ route('classes.edit', $class) }}" class="p-1.5 text-slate-400 hover:text-slate-700 transition" title="Edit Class">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </a>
                        </div>
                        <a href="{{ route('sessions.create', ['class_id' => $class->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-xs transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Take Attendance
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
