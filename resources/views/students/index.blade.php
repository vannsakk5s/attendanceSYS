@extends('layouts.app')

@section('title', 'Students Directory')
@section('header', 'Students Directory')
@section('subtitle', 'View, search, and manage all students enrolled across your classes')

@section('actions')
    <a href="{{ route('students.import') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-sm font-semibold shadow-xs transition">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
        </svg>
        Bulk Import CSV
    </a>
    <a href="{{ route('students.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-sm transition shadow-indigo-500/25">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add Student
    </a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('students.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by student code or full name..."
                    class="w-full px-4 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="sm:w-64">
                <select name="class_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Classes</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->class_name }} ({{ $c->subject_name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold transition shrink-0">
                Filter
            </button>

            @if(request()->hasAny(['search', 'class_id']))
                <a href="{{ route('students.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-sm font-medium transition text-center shrink-0">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        @if($students->isEmpty())
            <div class="p-12 text-center">
                <div class="h-14 w-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 mt-4">No Students Found</h3>
                <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Try changing your search filters or enroll a new student.</p>
                <div class="mt-5 flex items-center justify-center gap-3">
                    <a href="{{ route('students.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold">
                        Add Student
                    </a>
                    <a href="{{ route('students.import') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">
                        Import CSV
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <th class="px-6 py-3.5">Student Code</th>
                            <th class="px-6 py-3.5">Full Name</th>
                            <th class="px-6 py-3.5">Enrolled Class</th>
                            <th class="px-6 py-3.5">Subject</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($students as $student)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-3.5 font-mono font-semibold text-slate-900 text-xs">
                                    {{ $student->student_code }}
                                </td>
                                <td class="px-6 py-3.5 font-semibold text-slate-800">
                                    <a href="{{ route('students.show', $student) }}" class="hover:text-indigo-600 transition">
                                        {{ $student->full_name }}
                                    </a>
                                </td>
                                <td class="px-6 py-3.5 text-xs text-slate-600">
                                    <a href="{{ route('classes.show', $student->schoolClass) }}" class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 font-semibold hover:bg-indigo-100 transition">
                                        {{ $student->schoolClass->class_name }}
                                    </a>
                                </td>
                                <td class="px-6 py-3.5 text-xs text-slate-500">
                                    {{ $student->schoolClass->subject_name }}
                                </td>
                                <td class="px-6 py-3.5 text-right space-x-2">
                                    <a href="{{ route('students.show', $student) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                                        Profile & Attendance
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

            @if($students->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $students->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
