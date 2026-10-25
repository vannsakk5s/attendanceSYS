@extends('layouts.app')

@section('title', $student->full_name . ' - Student Profile')
@section('header', $student->full_name)
@section('subtitle', 'Student ID: ' . $student->student_code . ' • Class: ' . $student->schoolClass->class_name)

@section('actions')
    <a href="{{ route('students.edit', $student) }}" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-sm font-semibold shadow-xs transition">
        Edit Profile
    </a>
    <a href="{{ route('classes.show', $student->schoolClass) }}" class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-sm font-semibold transition">
        View Class Roster
    </a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Student Header & Attendance KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <!-- Profile Card -->
        <div class="md:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center gap-4">
            <div class="h-16 w-16 rounded-2xl bg-gradient-to-tr from-indigo-600 to-indigo-400 text-white font-extrabold text-2xl flex items-center justify-center shrink-0 shadow-md shadow-indigo-500/20">
                {{ substr($student->full_name, 0, 1) }}
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900">{{ $student->full_name }}</h3>
                <p class="text-xs font-mono text-slate-500 mt-0.5">Code: {{ $student->student_code }}</p>
                <div class="mt-2 flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700">
                        {{ $student->schoolClass->class_name }}
                    </span>
                    <span class="text-xs text-slate-400 font-medium">
                        {{ $student->schoolClass->subject_name }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Attendance Rate -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-col justify-between">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Attendance Rate</p>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-3xl font-extrabold text-slate-900">{{ $attendanceRate }}%</span>
                <span class="text-xs text-slate-400">overall</span>
            </div>
            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mt-3">
                <div class="bg-indigo-600 h-full rounded-full transition-all" style="width: {{ min(100, $attendanceRate) }}%"></div>
            </div>
        </div>

        <!-- Attendance Counts Breakdown -->
        <div class="md:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Breakdown ({{ $totalRecords }} Sessions)</p>
            <div class="grid grid-cols-4 gap-2 text-center">
                <div class="p-2 rounded-xl bg-emerald-50 text-emerald-800">
                    <span class="block text-xl font-black text-emerald-700">{{ $presentRecords }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Present</span>
                </div>
                <div class="p-2 rounded-xl bg-amber-50 text-amber-800">
                    <span class="block text-xl font-black text-amber-700">{{ $lateRecords }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Late</span>
                </div>
                <div class="p-2 rounded-xl bg-rose-50 text-rose-800">
                    <span class="block text-xl font-black text-rose-700">{{ $absentRecords }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600">Absent</span>
                </div>
                <div class="p-2 rounded-xl bg-blue-50 text-blue-800">
                    <span class="block text-xl font-black text-blue-700">{{ $excusedRecords }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600">Excused</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance History Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Attendance Session History</h3>
            <span class="text-xs text-slate-400">{{ $student->attendanceRecords->count() }} records</span>
        </div>

        @if($student->attendanceRecords->isEmpty())
            <div class="p-8 text-center text-sm text-slate-500">
                No attendance sessions recorded yet for this student.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <th class="px-6 py-3.5">Session Date</th>
                            <th class="px-6 py-3.5">Time</th>
                            <th class="px-6 py-3.5">Attendance Status</th>
                            <th class="px-6 py-3.5">Teacher Note</th>
                            <th class="px-6 py-3.5 text-right">Session</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($student->attendanceRecords as $record)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-3.5 font-bold text-slate-900 text-xs">
                                    {{ $record->attendanceSession->session_date->format('l, M d, Y') }}
                                </td>
                                <td class="px-6 py-3.5 font-mono text-slate-500 text-xs">
                                    {{ \Carbon\Carbon::parse($record->attendanceSession->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($record->attendanceSession->end_time)->format('H:i') }}
                                </td>
                                <td class="px-6 py-3.5">
                                    @if($record->status === 'present')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            Present
                                        </span>
                                    @elseif($record->status === 'late')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                            Late
                                        </span>
                                    @elseif($record->status === 'absent')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                                            Absent
                                        </span>
                                    @elseif($record->status === 'excused')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            Excused
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-xs text-slate-600">
                                    {{ $record->note ?? '—' }}
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    <a href="{{ route('sessions.show', $record->attendanceSession) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                                        Open Session &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
