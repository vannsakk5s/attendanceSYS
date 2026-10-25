@extends('layouts.app')

@section('title', 'Edit Student')
@section('header', 'Edit Student')
@section('subtitle', 'Update student details')

@section('actions')
    <a href="{{ route('students.show', $student) }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-sm font-semibold shadow-xs transition">
        Cancel
    </a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 md:p-8">
        <form method="POST" action="{{ route('students.update', $student) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="class_id" class="block text-sm font-semibold text-slate-700 mb-1">
                    Class & Subject <span class="text-rose-500">*</span>
                </label>
                <select id="class_id" name="class_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition">
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ old('class_id', $student->class_id) == $c->id ? 'selected' : '' }}>
                            {{ $c->class_name }} ({{ $c->subject_name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="student_code" class="block text-sm font-semibold text-slate-700 mb-1">
                    Student Code / ID <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="student_code" name="student_code" value="{{ old('student_code', $student->student_code) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-mono transition">
            </div>

            <div>
                <label for="full_name" class="block text-sm font-semibold text-slate-700 mb-1">
                    Full Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $student->full_name) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition">
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('students.show', $student) }}" class="px-4 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-sm transition shadow-indigo-500/25">
                    Update Student
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
