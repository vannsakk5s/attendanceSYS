@extends('layouts.app')

@section('title', 'Edit Class')
@section('header', 'Edit ' . $class->class_name)
@section('subtitle', 'Update class name and subject')

@section('actions')
    <a href="{{ route('classes.show', $class) }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-sm font-semibold shadow-xs transition">
        Cancel
    </a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 md:p-8">
        <form method="POST" action="{{ route('classes.update', $class) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="class_name" class="block text-sm font-semibold text-slate-700 mb-1">
                    Class Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="class_name" name="class_name" value="{{ old('class_name', $class->class_name) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition">
            </div>

            <div>
                <label for="subject_name" class="block text-sm font-semibold text-slate-700 mb-1">
                    Subject Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="subject_name" name="subject_name" value="{{ old('subject_name', $class->subject_name) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition">
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <form method="POST" action="{{ route('classes.destroy', $class) }}" onsubmit="return confirm('Are you sure you want to delete this class? All associated students, schedules, and attendance sessions will be removed.');">
                    <!-- delete button handled separately outside main form to avoid nested form -->
                </form>
                <a href="{{ route('classes.show', $class) }}" class="px-4 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-sm transition shadow-indigo-500/25">
                    Update Class
                </button>
            </div>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-200 flex items-center justify-between">
            <div>
                <h4 class="text-sm font-bold text-rose-600">Danger Zone</h4>
                <p class="text-xs text-slate-500">Permanently delete this class, its students, schedules, and attendance records.</p>
            </div>
            <form method="POST" action="{{ route('classes.destroy', $class) }}" onsubmit="return confirm('Are you sure you want to delete this class? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-xl text-xs font-semibold transition border border-rose-200">
                    Delete Class
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
