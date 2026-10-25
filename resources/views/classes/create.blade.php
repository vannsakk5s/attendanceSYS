@extends('layouts.app')

@section('title', 'Create New Class')
@section('header', 'Create New Class')
@section('subtitle', 'Define a new class and assign its subject')

@section('actions')
    <a href="{{ route('classes.index') }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-sm font-semibold shadow-xs transition">
        Cancel
    </a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 md:p-8">
        <form method="POST" action="{{ route('classes.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="class_name" class="block text-sm font-semibold text-slate-700 mb-1">
                    Class Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="class_name" name="class_name" value="{{ old('class_name') }}" required autofocus
                    placeholder="e.g. MIS-Y2-A, Grade 10B, CS-Year-3"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition">
                <p class="text-xs text-slate-400 mt-1">The name or section identifier for this group of students.</p>
            </div>

            <div>
                <label for="subject_name" class="block text-sm font-semibold text-slate-700 mb-1">
                    Subject Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="subject_name" name="subject_name" value="{{ old('subject_name') }}" required
                    placeholder="e.g. Java Programming, Database Systems, Mathematics"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition">
                <p class="text-xs text-slate-400 mt-1">The subject or course you will teach this class.</p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('classes.index') }}" class="px-4 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-sm transition shadow-indigo-500/25">
                    Save Class
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
