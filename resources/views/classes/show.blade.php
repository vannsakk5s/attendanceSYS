@extends('layouts.app')

@section('title', $class->class_name . ' - ' . $class->subject_name)
@section('header', $class->class_name)
@section('subtitle', 'Subject: ' . $class->subject_name)

@section('actions')
    <a href="{{ route('sessions.create', ['class_id' => $class->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-sm transition shadow-indigo-500/25">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Take Attendance
    </a>
    <a href="{{ route('reports.show', $class) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-sm font-semibold shadow-xs transition">
        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        Reports
    </a>
    <a href="{{ route('classes.edit', $class) }}" class="p-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-sm font-semibold shadow-xs transition" title="Edit Class Details">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
        </svg>
    </a>
@endsection

@section('content')
<div class="space-y-6" x-data="{
    tab: '{{ request('tab', 'students') }}',
    shareModalOpen: false,
    shareModalTitle: '',
    shareModalDate: '',
    shareModalUrl: '',
    shareModalCopied: false,
    openShareModal(url, title, date) {
        this.shareModalUrl = url;
        this.shareModalTitle = title;
        this.shareModalDate = date;
        this.shareModalCopied = false;
        this.shareModalOpen = true;
        if (window.copyTextToClipboard) {
            window.copyTextToClipboard(url);
        }
    },
    copyShareModalUrl() {
        if (window.copyTextToClipboard) {
            window.copyTextToClipboard(this.shareModalUrl);
        }
        this.shareModalCopied = true;
        setTimeout(() => this.shareModalCopied = false, 2500);
    }
}">
    <!-- Class Info Strip -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-lg shadow-sm shadow-indigo-600/30">
                {{ substr($class->class_name, 0, 2) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-slate-900">{{ $class->class_name }}</h2>
                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700">
                        {{ $class->subject_name }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Teacher: {{ $class->teacher->name }}</p>
            </div>
        </div>

        <div class="flex items-center gap-6 text-center text-xs">
            <div>
                <span class="block text-xl font-extrabold text-slate-900">{{ $class->students->count() }}</span>
                <span class="text-slate-400 font-medium">Students</span>
            </div>
            <div class="h-8 w-px bg-slate-200"></div>
            <div>
                <span class="block text-xl font-extrabold text-slate-900">{{ $class->schedules->count() }}</span>
                <span class="text-slate-400 font-medium">Schedules</span>
            </div>
            <div class="h-8 w-px bg-slate-200"></div>
            <div>
                <span class="block text-xl font-extrabold text-slate-900">{{ $class->attendanceSessions->count() }}</span>
                <span class="text-slate-400 font-medium">Sessions</span>
            </div>
        </div>
    </div>

    <!-- Tab Buttons -->
    <div class="flex border-b border-slate-200 gap-2">
        <button @click="tab = 'students'" :class="tab === 'students' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'"
            class="py-3 px-4 border-b-2 text-sm transition cursor-pointer flex items-center gap-2">
            <span>Enrolled Students</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold" :class="tab === 'students' ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-600'">
                {{ $class->students->count() }}
            </span>
        </button>

        <button @click="tab = 'schedules'" :class="tab === 'schedules' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'"
            class="py-3 px-4 border-b-2 text-sm transition cursor-pointer flex items-center gap-2">
            <span>Class Schedules</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold" :class="tab === 'schedules' ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-600'">
                {{ $class->schedules->count() }}
            </span>
        </button>

        <button @click="tab = 'sessions'" :class="tab === 'sessions' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'"
            class="py-3 px-4 border-b-2 text-sm transition cursor-pointer flex items-center gap-2">
            <span>Attendance Sessions</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold" :class="tab === 'sessions' ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-600'">
                {{ $class->attendanceSessions->count() }}
            </span>
        </button>
    </div>

    <!-- TAB 1: STUDENTS ROSTER -->
    <div x-show="tab === 'students'" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-900">Student Roster</h3>
                <p class="text-xs text-slate-500">Students enrolled directly in {{ $class->class_name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('students.import', ['class_id' => $class->id]) }}" class="px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold shadow-xs transition inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import CSV
                </a>
                <a href="{{ route('students.create', ['class_id' => $class->id]) }}" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-xs transition inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Student
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            @if($class->students->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-sm text-slate-500 font-medium">No students added to this class yet.</p>
                    <div class="mt-4 flex items-center justify-center gap-3">
                        <a href="{{ route('students.create', ['class_id' => $class->id]) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold">
                            + Add Student Manually
                        </a>
                        <a href="{{ route('students.import', ['class_id' => $class->id]) }}" class="px-4 py-2 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl text-xs font-semibold">
                            Bulk Import CSV
                        </a>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200/80">
                            <tr>
                                <th class="px-6 py-3.5">#</th>
                                <th class="px-6 py-3.5">Student Code</th>
                                <th class="px-6 py-3.5">Full Name</th>
                                <th class="px-6 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($class->students as $index => $student)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-3.5 text-slate-400 text-xs font-mono">{{ $index + 1 }}</td>
                                    <td class="px-6 py-3.5 font-mono font-semibold text-slate-900 text-xs">{{ $student->student_code }}</td>
                                    <td class="px-6 py-3.5 font-semibold text-slate-800">
                                        <a href="{{ route('students.show', $student) }}" class="hover:text-indigo-600 transition">
                                            {{ $student->full_name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-3.5 text-right space-x-2">
                                        <a href="{{ route('students.show', $student) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                                            History
                                        </a>
                                        <a href="{{ route('students.edit', $student) }}" class="text-xs text-slate-500 hover:text-slate-800 font-medium">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('students.destroy', $student) }}" class="inline" onsubmit="return confirm('Remove student {{ $student->full_name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 font-medium cursor-pointer">
                                                Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- TAB 2: CLASS SCHEDULES -->
    <div x-show="tab === 'schedules'" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Schedule List (2 cols) -->
            <div class="lg:col-span-2 space-y-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Current Weekly Schedule</h3>
                    <p class="text-xs text-slate-500">Days and times when this class meets</p>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    @if($class->schedules->isEmpty())
                        <div class="p-8 text-center">
                            <p class="text-sm text-slate-500">No schedules set for this class.</p>
                            <p class="text-xs text-slate-400 mt-1">Use the form on the right to add teaching days and times.</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-100">
                            @foreach($class->schedules as $sched)
                                <div class="p-4 flex items-center justify-between hover:bg-slate-50/70 transition">
                                    <div class="flex items-center gap-3">
                                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-sm font-bold text-slate-900">{{ $sched->day_of_week }}</h4>
                                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700">
                                                    {{ $sched->shift }}
                                                </span>
                                            </div>
                                            <p class="text-xs font-mono text-slate-500 mt-0.5">
                                                {{ \Carbon\Carbon::parse($sched->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($sched->end_time)->format('H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('classes.schedules.destroy', [$class, $sched]) }}" onsubmit="return confirm('Remove schedule for {{ $sched->day_of_week }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-500 rounded-lg transition" title="Delete Schedule">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Add Schedule Form (1 col) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 h-fit">
                <h4 class="text-sm font-bold text-slate-900 mb-1">+ Add New Schedule</h4>
                <p class="text-xs text-slate-500 mb-4">Add a new recurring session schedule</p>

                <form method="POST" action="{{ route('classes.schedules.store', $class) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="day_of_week" class="block text-xs font-semibold text-slate-700 mb-1">Day of Week</label>
                        <select name="day_of_week" id="day_of_week" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500">
                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                <option value="{{ $day }}">{{ $day }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="shift" class="block text-xs font-semibold text-slate-700 mb-1">Shift</label>
                        <select name="shift" id="shift" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500">
                            <option value="Morning">Morning</option>
                            <option value="Afternoon">Afternoon</option>
                            <option value="Evening">Evening</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="start_time" class="block text-xs font-semibold text-slate-700 mb-1">Start Time</label>
                            <input type="time" name="start_time" id="start_time" value="08:00" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="end_time" class="block text-xs font-semibold text-slate-700 mb-1">End Time</label>
                            <input type="time" name="end_time" id="end_time" value="09:30" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                        Add Schedule
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- TAB 3: ATTENDANCE SESSIONS -->
    <div x-show="tab === 'sessions'" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-900">Attendance Sessions</h3>
                <p class="text-xs text-slate-500">All historical sessions recorded for {{ $class->class_name }}</p>
            </div>
            <a href="{{ route('sessions.create', ['class_id' => $class->id]) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-xs transition inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Take Attendance Now
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            @if($class->attendanceSessions->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-sm text-slate-500">No attendance sessions recorded yet for this class.</p>
                    <a href="{{ route('sessions.create', ['class_id' => $class->id]) }}" class="mt-3 inline-block px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold">
                        Start First Attendance Session
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200/80">
                            <tr>
                                <th class="px-6 py-3.5">Session Date</th>
                                <th class="px-6 py-3.5">Time Window</th>
                                <th class="px-6 py-3.5">Breakdown</th>
                                <th class="px-6 py-3.5 text-center">Attendance %</th>
                                <th class="px-6 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($class->attendanceSessions as $sess)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-3.5 font-bold text-slate-900 text-xs">
                                        {{ $sess->session_date->format('l, M d, Y') }}
                                    </td>
                                    <td class="px-6 py-3.5 font-mono text-slate-600 text-xs">
                                        {{ \Carbon\Carbon::parse($sess->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($sess->end_time)->format('H:i') }}
                                    </td>
                                    <td class="px-6 py-3.5 text-xs">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-semibold">{{ $sess->present_count }} P</span>
                                            <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 font-semibold">{{ $sess->late_count }} L</span>
                                            <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-700 font-semibold">{{ $sess->absent_count }} A</span>
                                            <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-semibold">{{ $sess->excused_count }} E</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5 text-center font-bold text-slate-800 text-xs">
                                        {{ $sess->attendance_rate }}%
                                    </td>
                                    <td class="px-6 py-3.5 text-right">
                                        <div class="inline-flex items-center gap-1.5">
                                            <!-- Share Link Only (Copies link directly to clipboard) -->
                                            <button type="button"
                                                onclick="copySessionLink(this, '{{ $sess->share_url }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 shadow-2xs transition cursor-pointer"
                                                title="Copy student attendance link">
                                                <svg class="w-3.5 h-3.5 text-indigo-600 share-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                </svg>
                                                <span class="share-label">Share Link</span>
                                            </button>

                                            <!-- Mark / Edit -->
                                            <a href="{{ route('sessions.show', $sess) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold transition">
                                                Mark / Edit
                                            </a>

                                            <!-- Delete -->
                                            <form method="POST" action="{{ route('sessions.destroy', $sess) }}" class="inline" onsubmit="return confirm('Delete this attendance session?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="Delete session">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <script>
        function copySessionLink(btn, url) {
            if (window.copyTextToClipboard) {
                window.copyTextToClipboard(url);
            }
            const label = btn.querySelector('.share-label');
            const icon = btn.querySelector('.share-icon');

            btn.classList.remove('bg-white', 'hover:bg-indigo-50', 'text-indigo-700', 'border-indigo-200');
            btn.classList.add('bg-emerald-50', 'hover:bg-emerald-100', 'text-emerald-700', 'border-emerald-300');
            if (label) label.textContent = 'Copied!';
            if (icon) {
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
                icon.classList.remove('text-indigo-600');
                icon.classList.add('text-emerald-600');
            }

            setTimeout(function() {
                btn.classList.remove('bg-emerald-50', 'hover:bg-emerald-100', 'text-emerald-700', 'border-emerald-300');
                btn.classList.add('bg-white', 'hover:bg-indigo-50', 'text-indigo-700', 'border-indigo-200');
                if (label) label.textContent = 'Share Link';
                if (icon) {
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />';
                    icon.classList.remove('text-emerald-600');
                    icon.classList.add('text-indigo-600');
                }
            }, 2500);
        }
    </script>
</div>
@endsection
