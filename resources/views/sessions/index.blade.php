@extends('layouts.app')

@section('title', 'Attendance Sessions')
@section('header', 'Attendance Sessions')
@section('subtitle', 'History of all recorded attendance sessions across your classes')

@section('actions')
    <a href="{{ route('sessions.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-sm transition shadow-indigo-500/25">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Session
    </a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('sessions.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <select name="class_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Classes</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->class_name }} ({{ $c->subject_name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:w-56">
                <input type="date" name="date" value="{{ request('date') }}"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold transition shrink-0">
                Filter
            </button>

            @if(request()->hasAny(['class_id', 'date']))
                <a href="{{ route('sessions.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-sm font-medium transition text-center shrink-0">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Sessions List -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        @if($sessions->isEmpty())
            <div class="p-12 text-center">
                <div class="h-14 w-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 mt-4">No Sessions Found</h3>
                <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Create an attendance session to start taking attendance for your students.</p>
                <div class="mt-5">
                    <a href="{{ route('sessions.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold">
                        + Start Attendance Session
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <th class="px-6 py-3.5">Date</th>
                            <th class="px-6 py-3.5">Class & Subject</th>
                            <th class="px-6 py-3.5">Time Window</th>
                            <th class="px-6 py-3.5">Status Breakdown</th>
                            <th class="px-6 py-3.5 text-center">Attendance %</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($sessions as $sess)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-3.5 font-bold text-slate-900 text-xs">
                                    {{ $sess->session_date->format('M d, Y') }}
                                    <span class="block text-[11px] text-slate-400 font-normal">{{ $sess->session_date->format('l') }}</span>
                                </td>
                                <td class="px-6 py-3.5 text-xs">
                                    <a href="{{ route('classes.show', $sess->schoolClass) }}" class="font-bold text-slate-800 hover:text-indigo-600 transition">
                                        {{ $sess->schoolClass->class_name }}
                                    </a>
                                    <span class="block text-slate-500 text-[11px]">{{ $sess->schoolClass->subject_name }}</span>
                                </td>
                                <td class="px-6 py-3.5 font-mono text-slate-600 text-xs">
                                    {{ \Carbon\Carbon::parse($sess->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($sess->end_time)->format('H:i') }}
                                </td>
                                <td class="px-6 py-3.5 text-xs">
                                    <div class="flex items-center gap-1.5 font-semibold text-[11px]">
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700">{{ $sess->present_count }} P</span>
                                        <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700">{{ $sess->late_count }} L</span>
                                        <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-700">{{ $sess->absent_count }} A</span>
                                        <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700">{{ $sess->excused_count }} E</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5 text-center font-extrabold text-slate-800 text-xs">
                                    {{ $sess->attendance_rate }}%
                                </td>
                                <td class="px-6 py-3.5 text-right" x-data="{ copied: false, shareUrl: '{{ $sess->share_url }}' }">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button"
                                            @click="window.copyTextToClipboard(shareUrl); copied = true; setTimeout(() => copied = false, 2500)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition shadow-2xs cursor-pointer"
                                            :class="copied ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-white hover:bg-indigo-50 text-indigo-700 border-indigo-200'"
                                            :title="copied ? 'Copied!' : 'Copy student attendance link'">
                                            <svg class="w-3.5 h-3.5" :class="copied ? 'text-emerald-600' : 'text-indigo-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path x-show="!copied" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                <path x-show="copied" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span x-text="copied ? 'Copied!' : 'Share Link'" :class="copied ? 'text-emerald-700 font-bold' : ''">Share Link</span>
                                        </button>

                                        <a href="{{ route('sessions.show', $sess) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold transition">
                                            Open Roster
                                        </a>

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

            @if($sessions->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $sessions->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
