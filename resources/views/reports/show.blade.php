@extends('layouts.app')

@section('title', 'Attendance Report - ' . $class->class_name)
@section('header', 'Attendance Report: ' . $class->class_name)
@section('subtitle', $class->subject_name . ' • Teacher: ' . $class->teacher->name)

@section('actions')
    <a href="{{ route('reports.export', $class) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-sm font-semibold shadow-xs transition">
        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
        Export CSV
    </a>
    <a href="{{ route('reports.print', $class) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-sm font-semibold transition">
        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
        </svg>
        Print View
    </a>
    <a href="{{ route('reports.index') }}" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-sm font-semibold shadow-xs transition">
        Back to Reports
    </a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Class Info Strip -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-900">{{ $class->class_name }} — {{ $class->subject_name }}</h3>
            <p class="text-xs text-slate-400 mt-0.5">Based on {{ count($reportData['sessions']) }} total attendance sessions conducted.</p>
        </div>
        <div class="flex items-center gap-4 text-xs font-semibold">
            <span class="text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md">Present (100%)</span>
            <span class="text-amber-700 bg-amber-50 px-2.5 py-1 rounded-md">Late (Attended)</span>
            <span class="text-rose-700 bg-rose-50 px-2.5 py-1 rounded-md">Absent</span>
            <span class="text-blue-700 bg-blue-50 px-2.5 py-1 rounded-md">Excused</span>
        </div>
    </div>

    <!-- Student Attendance Matrix Table -->
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
                        <th class="px-4 py-3.5 text-center">Total</th>
                        <th class="px-5 py-3.5 text-right">Attendance Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reportData['students'] as $index => $item)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5 text-xs text-slate-400 font-mono">{{ $index + 1 }}</td>
                            <td class="px-5 py-3.5 text-xs font-mono font-bold text-slate-900">
                                {{ $item['student']->student_code }}
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-slate-800 text-xs">
                                <a href="{{ route('students.show', $item['student']) }}" class="hover:text-indigo-600 transition">
                                    {{ $item['student']->full_name }}
                                </a>
                            </td>
                            <td class="px-4 py-3.5 text-center font-bold text-emerald-600 text-xs">{{ $item['present'] }}</td>
                            <td class="px-4 py-3.5 text-center font-bold text-amber-600 text-xs">{{ $item['late'] }}</td>
                            <td class="px-4 py-3.5 text-center font-bold text-rose-600 text-xs">{{ $item['absent'] }}</td>
                            <td class="px-4 py-3.5 text-center font-bold text-blue-600 text-xs">{{ $item['excused'] }}</td>
                            <td class="px-4 py-3.5 text-center font-semibold text-slate-700 text-xs">{{ $item['total_sessions'] }}</td>
                            <td class="px-5 py-3.5 text-right font-extrabold text-xs">
                                <span class="px-2.5 py-1 rounded-full {{ $item['rate'] >= 80 ? 'bg-emerald-50 text-emerald-700' : ($item['rate'] >= 60 ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                                    {{ $item['rate'] }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-500">
                                No student records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
