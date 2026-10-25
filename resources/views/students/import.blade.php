@extends('layouts.app')

@section('title', 'Bulk Import Students')
@section('header', 'Bulk Import Students')
@section('subtitle', 'Upload an Excel (.xlsx) spreadsheet or CSV file to enroll students')

@section('actions')
    <a href="{{ route('students.import.excel-template') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold shadow-xs transition shadow-emerald-600/25">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Download Excel (.xlsx) Template
    </a>
    <a href="{{ route('students.import.template') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-sm font-semibold shadow-xs transition">
        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
        Download CSV Template
    </a>
    <a href="{{ route('students.index') }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-sm font-semibold shadow-xs transition">
        Cancel
    </a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 md:p-8">
        <form method="POST" action="{{ route('students.import.process') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="class_id" class="block text-sm font-semibold text-slate-700 mb-1">
                    Select Target Class <span class="text-rose-500">*</span>
                </label>
                <select id="class_id" name="class_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-sm transition">
                    <option value="">-- Choose Class --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ old('class_id', $selectedClassId) == $c->id ? 'selected' : '' }}>
                            {{ $c->class_name }} ({{ $c->subject_name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="file" class="block text-sm font-semibold text-slate-700 mb-1">
                    Upload Excel (.xlsx) or CSV File <span class="text-rose-500">*</span>
                </label>
                <input type="file" id="file" name="file" accept=".xlsx,.xls,.csv,text/csv,text/plain" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="text-xs text-slate-400 mt-1">Accepted formats: <strong class="text-slate-600">Excel (.xlsx, .xls)</strong> or <strong class="text-slate-600">CSV (.csv)</strong> with columns <code class="font-mono text-indigo-600 font-semibold">student_code</code> and <code class="font-mono text-indigo-600 font-semibold">full_name</code>.</p>
            </div>

            <!-- Preview Format Box -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 text-xs">
                <div class="flex items-center justify-between mb-2">
                    <p class="font-bold text-slate-700">Expected Column Structure:</p>
                    <span class="text-[11px] text-slate-400">Header row required</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left font-mono border-collapse bg-white rounded-lg border border-slate-200">
                        <thead class="bg-indigo-50/70 text-indigo-900 border-b border-slate-200">
                            <tr>
                                <th class="p-2 border-r border-slate-200">student_code</th>
                                <th class="p-2">full_name</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <tr>
                                <td class="p-2 border-r border-slate-200">STU-00101</td>
                                <td class="p-2">Sophea Chan</td>
                            </tr>
                            <tr>
                                <td class="p-2 border-r border-slate-200">STU-00102</td>
                                <td class="p-2">Bopha Kim</td>
                            </tr>
                            <tr>
                                <td class="p-2 border-r border-slate-200">STU-00103</td>
                                <td class="p-2">Rathana Seng</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('students.index') }}" class="px-4 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-sm transition shadow-indigo-500/25">
                    Start Import
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
