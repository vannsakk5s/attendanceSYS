@extends('layouts.app')

@section('title', 'Attendance Reports')
@section('header', 'Attendance Reports')
@section('subtitle', 'Class-level attendance metrics and performance summaries')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($classes as $item)
            @php $c = $item['class']; @endphp
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 flex flex-col justify-between hover:border-indigo-300 hover:shadow-md transition">
                <div>
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">{{ $c->class_name }}</h3>
                            <p class="text-xs text-indigo-600 font-semibold">{{ $c->subject_name }}</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                            {{ $item['total_students'] }} Students
                        </span>
                    </div>

                    <div class="mt-6 space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Recorded Sessions:</span>
                            <span class="font-bold text-slate-800">{{ $item['total_sessions'] }}</span>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Overall Attendance Rate:</span>
                            <span class="font-extrabold text-indigo-600 text-sm">{{ $item['attendance_rate'] }}%</span>
                        </div>

                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-indigo-600 h-full rounded-full" style="width: {{ min(100, $item['attendance_rate']) }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('reports.export', $c) }}" class="text-xs text-slate-600 hover:text-slate-900 font-semibold inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export CSV
                    </a>
                    <a href="{{ route('reports.show', $c) }}" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                        View Matrix &rarr;
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white p-12 text-center rounded-2xl border border-slate-200">
                <p class="text-sm text-slate-500">No classes found to generate reports.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
