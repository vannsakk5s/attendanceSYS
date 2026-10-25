@extends('layouts.app')

@section('title', 'Start Attendance Session')
@section('header', 'New Attendance Session')
@section('subtitle', 'Select a class, choose a preset schedule or specify custom start/end times')

@section('actions')
    <a href="{{ url()->previous(route('sessions.index')) }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-sm font-semibold shadow-xs transition">
        Cancel
    </a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 md:p-8">
        <form method="POST" action="{{ route('sessions.store') }}" class="space-y-6">
            @csrf

            <!-- 1. Select Class -->
            <div>
                <label for="class_id" class="block text-sm font-semibold text-slate-700 mb-1">
                    Select Class <span class="text-rose-500">*</span>
                </label>
                <select id="class_id" name="class_id" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-sm transition">
                    <option value="">-- Choose Class --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ old('class_id', $selectedClassId) == $c->id ? 'selected' : '' }}>
                            {{ $c->class_name }} ({{ $c->subject_name }}) — {{ $c->students_count }} enrolled students
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-400 mt-1">Attendance records will be initialized for all enrolled students in this class.</p>
            </div>

            <!-- 2. Session Date -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="session_date" class="block text-sm font-semibold text-slate-700">
                        Session Date <span class="text-rose-500">*</span>
                    </label>
                    <span id="day_badge" class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-md"></span>
                </div>
                <input type="date" id="session_date" name="session_date" value="{{ old('session_date', $defaultDate) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-sm transition">
            </div>

            <!-- 3. Select Schedule (Option 1 vs Option 2) -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="schedule_select" class="block text-sm font-semibold text-slate-700">
                        Select Schedule <span class="text-rose-500">*</span>
                    </label>
                    <span id="schedule_count_badge" class="text-[11px] text-slate-400"></span>
                </div>

                <select id="schedule_select" name="schedule_id"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-sm transition">
                    <!-- Populated dynamically by JavaScript -->
                    <option value="custom">-- Option 2: Custom Time (Select custom start / end time) --</option>
                </select>

                <!-- Feedback banner on schedule selection -->
                <div id="sync_status" class="mt-2 text-xs flex items-center justify-between min-h-[1.25rem]">
                    <!-- Updated dynamically -->
                </div>
            </div>

            <!-- 4. Start Time & End Time -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-sm font-semibold text-slate-700 mb-1">
                        Start Time <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" id="start_time" name="start_time" value="{{ old('start_time', $defaultStartTime) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-sm font-mono transition">
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-semibold text-slate-700 mb-1">
                        End Time <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" id="end_time" name="end_time" value="{{ old('end_time', $defaultEndTime) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-sm font-mono transition">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ url()->previous(route('sessions.index')) }}" class="px-4 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-sm transition shadow-indigo-500/25">
                    Start Session & Take Attendance
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const classes = @json($classes);
    const classSelect = document.getElementById('class_id');
    const dateInput = document.getElementById('session_date');
    const scheduleSelect = document.getElementById('schedule_select');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const dayBadge = document.getElementById('day_badge');
    const syncStatus = document.getElementById('sync_status');
    const countBadge = document.getElementById('schedule_count_badge');

    const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    function getSelectedDay() {
        if (!dateInput.value) return '';
        const d = new Date(dateInput.value + 'T00:00:00');
        return isNaN(d.getTime()) ? '' : dayNames[d.getDay()];
    }

    function renderSchedules(preferredScheduleId = null) {
        const classId = classSelect.value;
        const selectedDay = getSelectedDay();

        if (dayBadge) {
            dayBadge.textContent = selectedDay || '';
            dayBadge.style.display = selectedDay ? 'inline-block' : 'none';
        }

        const cls = classes.find(c => c.id == classId);
        const schedules = (cls && cls.schedules) ? cls.schedules : [];

        if (countBadge) {
            countBadge.textContent = schedules.length > 0 ? `${schedules.length} schedule(s) found` : 'No schedules';
        }

        // Clear existing options
        scheduleSelect.innerHTML = '';

        if (schedules.length === 0) {
            const emptyOpt = document.createElement('option');
            emptyOpt.value = 'custom';
            emptyOpt.textContent = 'No preset schedules for this class — Option 2: Custom Time';
            scheduleSelect.appendChild(emptyOpt);
            updateStatus('custom');
            return;
        }

        let dayMatchedId = null;

        schedules.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            const startH = s.start_time.substring(0, 5);
            const endH = s.end_time.substring(0, 5);
            const isMatch = selectedDay && s.day_of_week.toLowerCase() === selectedDay.toLowerCase();

            if (isMatch && !dayMatchedId) {
                dayMatchedId = s.id;
            }

            opt.textContent = `${s.day_of_week} (${s.shift}) • ${startH} - ${endH}${isMatch ? ' ★ [Matches ' + selectedDay + ']' : ''}`;
            opt.dataset.start = startH;
            opt.dataset.end = endH;
            scheduleSelect.appendChild(opt);
        });

        // Add Option 2: Custom Time
        const customOpt = document.createElement('option');
        customOpt.value = 'custom';
        customOpt.textContent = '-- Option 2: Custom Time (Select custom start / end time) --';
        scheduleSelect.appendChild(customOpt);

        // Preselect: preferredScheduleId, or day-match schedule, or first schedule
        const toSelect = preferredScheduleId || dayMatchedId || schedules[0].id;
        scheduleSelect.value = toSelect;
        applySchedule(toSelect);
    }

    function applySchedule(scheduleId) {
        if (!scheduleId || scheduleId === 'custom') {
            updateStatus('custom');
            return;
        }

        const opt = scheduleSelect.querySelector(`option[value="${scheduleId}"]`);
        if (opt && opt.dataset.start && opt.dataset.end) {
            startTimeInput.value = opt.dataset.start;
            endTimeInput.value = opt.dataset.end;
            updateStatus('synced', opt.textContent.replace(/ ★ \[Matches [^\]]+\]/, ''));
        }
    }

    function updateStatus(type, label = '') {
        if (!syncStatus) return;
        if (type === 'synced') {
            syncStatus.innerHTML = `
                <span class="text-emerald-700 font-semibold flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0 inline" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Synced: ${label}
                </span>`;
        } else {
            syncStatus.innerHTML = `<span class="text-indigo-600 font-medium">Option 2: Custom time window. You can adjust Start Time and End Time below.</span>`;
        }
    }

    classSelect.addEventListener('change', function () {
        renderSchedules();
    });

    dateInput.addEventListener('change', function () {
        renderSchedules(scheduleSelect.value);
    });

    scheduleSelect.addEventListener('change', function () {
        applySchedule(this.value);
    });

    startTimeInput.addEventListener('input', function () {
        scheduleSelect.value = 'custom';
        updateStatus('custom');
    });

    endTimeInput.addEventListener('input', function () {
        scheduleSelect.value = 'custom';
        updateStatus('custom');
    });

    // Run on page load
    renderSchedules('{{ old('schedule_id', '') }}');
});
</script>
@endsection
