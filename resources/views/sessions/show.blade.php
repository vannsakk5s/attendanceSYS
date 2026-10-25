@extends('layouts.app')

@section('title', 'Take Attendance - ' . $session->schoolClass->class_name)
@section('header', 'Attendance Roster: ' . $session->schoolClass->class_name)
@section('subtitle', $session->schoolClass->subject_name . ' • ' . $session->session_date->format('l, M d, Y') . ' (' . \Carbon\Carbon::parse($session->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($session->end_time)->format('H:i') . ')')

@section('actions')
    <a href="{{ $session->share_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-xl text-sm font-semibold shadow-xs transition" title="Preview Student Session Portal">
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
        </svg>
        Student Link
    </a>
    <a href="{{ route('classes.show', [$session->schoolClass, 'tab' => 'sessions']) }}" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-sm font-semibold shadow-xs transition">
        &larr; Back to Class
    </a>
@endsection

@section('content')
<div class="space-y-6" x-data="{
    filter: 'all',
    search: '',
    copied: false,
    shareUrl: '{{ $session->share_url }}',
    markAll(status) {
        document.querySelectorAll(`input[type='radio'][value='${status}']`).forEach(el => {
            el.checked = true;
            el.dispatchEvent(new Event('change'));
        });
    }
}">
    <!-- Share Link for Students Banner -->
    <div class="bg-gradient-to-r from-indigo-50/90 via-white to-purple-50/70 rounded-2xl border border-indigo-100 p-4 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-900">Student Attendance Session Link</h3>
                    <p class="text-[11px] text-slate-500">Share this link with students in this session so they can see their attendance status and notes.</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <div class="relative min-w-[240px]">
                    <input type="text" readonly :value="shareUrl" @click="$el.select()"
                        class="w-full bg-white border border-slate-300 rounded-xl pl-3 pr-16 py-1.5 text-xs font-mono text-slate-700 select-all shadow-2xs">
                    <button type="button"
                        @click="navigator.clipboard.writeText(shareUrl); copied = true; setTimeout(() => copied = false, 2500)"
                        class="absolute right-1 top-1 bottom-1 px-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-lg transition"
                        title="Copy session link">
                        <span x-text="copied ? 'Copied!' : 'Copy'" :class="copied ? 'text-emerald-600' : ''"></span>
                    </button>
                </div>

                <a :href="shareUrl" target="_blank"
                    class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-xs transition shrink-0">
                    Open
                </a>

                <form action="{{ route('sessions.share-link.regenerate', $session) }}" method="POST"
                    onsubmit="return confirm('Regenerating will deactivate the previous session link for students. Continue?');"
                    class="inline shrink-0">
                    @csrf
                    <button type="submit" class="p-1.5 bg-white hover:bg-slate-50 text-slate-400 hover:text-slate-700 border border-slate-200 rounded-xl transition" title="Regenerate link">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <!-- Summary KPIs Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900">{{ $session->attendanceRecords->count() }} Total Students</h3>
                <p class="text-xs text-slate-400">Current Session Attendance Rate: <strong class="text-indigo-600">{{ $session->attendance_rate }}%</strong></p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200/60">
                {{ $session->present_count }} Present
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-amber-50 text-amber-800 text-xs font-bold border border-amber-200/60">
                {{ $session->late_count }} Late
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-rose-50 text-rose-800 text-xs font-bold border border-rose-200/60">
                {{ $session->absent_count }} Absent
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-blue-50 text-blue-800 text-xs font-bold border border-blue-200/60">
                {{ $session->excused_count }} Excused
            </span>
        </div>
    </div>

    <!-- Quick Batch Actions & Form -->
    <form method="POST" action="{{ route('sessions.records.update', $session) }}">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <!-- Table Header Toolbar -->
            <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-slate-500">Quick Fill:</span>
                    <button type="button" @click="markAll('present')" class="px-2.5 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 rounded-lg text-xs font-semibold transition cursor-pointer">
                        All Present
                    </button>
                    <button type="button" @click="markAll('absent')" class="px-2.5 py-1 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded-lg text-xs font-semibold transition cursor-pointer">
                        All Absent
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <input type="text" x-model="search" placeholder="Filter student..."
                        class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500">
                    <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold shadow-xs transition cursor-pointer">
                        Save Attendance
                    </button>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <th class="px-5 py-3 w-12">#</th>
                            <th class="px-5 py-3 w-32">Student Code</th>
                            <th class="px-5 py-3">Student Name</th>
                            <th class="px-5 py-3 w-96">Attendance Status</th>
                            <th class="px-5 py-3">Note / Reason</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($session->attendanceRecords as $index => $record)
                            <tr class="hover:bg-slate-50/70 transition"
                                x-show="search === '' || '{{ strtolower($record->student->student_code . ' ' . $record->student->full_name) }}'.includes(search.toLowerCase())">
                                <td class="px-5 py-3.5 text-xs text-slate-400 font-mono">{{ $index + 1 }}</td>
                                <td class="px-5 py-3.5 text-xs font-mono font-bold text-slate-900">
                                    {{ $record->student->student_code }}
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-slate-800 text-xs">
                                    <a href="{{ route('students.show', $record->student) }}" class="hover:text-indigo-600 transition" target="_blank">
                                        {{ $record->student->full_name }}
                                    </a>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="inline-flex rounded-xl bg-slate-100 p-1 gap-1 text-xs">
                                        <!-- Present -->
                                        <label class="cursor-pointer">
                                            <input type="radio" name="records[{{ $record->id }}][status]" value="present"
                                                {{ $record->status === 'present' ? 'checked' : '' }} class="sr-only peer">
                                            <span class="inline-block px-2.5 py-1 rounded-lg font-bold transition text-slate-600 peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:shadow-xs">
                                                Present
                                            </span>
                                        </label>

                                        <!-- Late -->
                                        <label class="cursor-pointer">
                                            <input type="radio" name="records[{{ $record->id }}][status]" value="late"
                                                {{ $record->status === 'late' ? 'checked' : '' }} class="sr-only peer">
                                            <span class="inline-block px-2.5 py-1 rounded-lg font-bold transition text-slate-600 peer-checked:bg-amber-500 peer-checked:text-white peer-checked:shadow-xs">
                                                Late
                                            </span>
                                        </label>

                                        <!-- Absent -->
                                        <label class="cursor-pointer">
                                            <input type="radio" name="records[{{ $record->id }}][status]" value="absent"
                                                {{ $record->status === 'absent' ? 'checked' : '' }} class="sr-only peer">
                                            <span class="inline-block px-2.5 py-1 rounded-lg font-bold transition text-slate-600 peer-checked:bg-rose-600 peer-checked:text-white peer-checked:shadow-xs">
                                                Absent
                                            </span>
                                        </label>

                                        <!-- Excused -->
                                        <label class="cursor-pointer">
                                            <input type="radio" name="records[{{ $record->id }}][status]" value="excused"
                                                {{ $record->status === 'excused' ? 'checked' : '' }} class="sr-only peer">
                                            <span class="inline-block px-2.5 py-1 rounded-lg font-bold transition text-slate-600 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:shadow-xs">
                                                Excused
                                            </span>
                                        </label>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <input type="text" name="records[{{ $record->id }}][note]" value="{{ old('records.'.$record->id.'.note', $record->note) }}"
                                        placeholder="Optional remark or note..."
                                        class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Sticky Bottom Save Bar -->
            <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
                <span class="text-xs text-slate-500">Ensure all student statuses are reviewed before submitting.</span>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition shadow-indigo-500/25">
                    Save Attendance Changes
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
