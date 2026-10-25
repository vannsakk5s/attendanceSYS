@extends('layouts.app')

@section('title', 'Add Student')
@section('header', 'Add Student')
@section('subtitle', 'Enroll a new student into a class')

@section('actions')
    <a href="{{ url()->previous(route('students.index')) }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-sm font-semibold shadow-xs transition">
        Cancel
    </a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 md:p-8">
        <form method="POST" action="{{ route('students.store') }}" class="space-y-6">
            @csrf

            @if(request()->filled('redirect_to_class'))
                <input type="hidden" name="redirect_to_class" value="1">
            @endif

            <div>
                <label for="class_id" class="block text-sm font-semibold text-slate-700 mb-1">
                    Class & Subject <span class="text-rose-500">*</span>
                </label>
                <select id="class_id" name="class_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition">
                    <option value="">-- Select Class --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ old('class_id', $selectedClassId) == $c->id ? 'selected' : '' }}>
                            {{ $c->class_name }} ({{ $c->subject_name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="student_code" class="block text-sm font-semibold text-slate-700 mb-1">
                    Student Code / ID <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="student_code" name="student_code" value="{{ old('student_code') }}" required
                    placeholder="e.g. STU-00101, 2026-CS-04"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-mono transition">
                <p class="text-xs text-slate-400 mt-1">Unique student identifier within this class.</p>
            </div>

            <div>
                <label for="full_name" class="block text-sm font-semibold text-slate-700 mb-1">
                    Full Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required
                    placeholder="e.g. John Doe, Sophea Chan"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition">
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ url()->previous(route('students.index')) }}" class="px-4 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-sm transition shadow-indigo-500/25">
                    Save Student
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
