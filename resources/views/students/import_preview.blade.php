@extends('layouts.app')

@section('title', 'CSV Import Preview')
@section('header', 'Import Preview: ' . $classroom->name)
@section('subtitle', 'Review the validated student rows before committing them to the database.')

@section('content')
<div class="space-y-6">
    <!-- Validation Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80">
            <span class="text-[10px] font-bold text-slate-400 uppercase">Target Classroom</span>
            <span class="text-base font-bold text-slate-900 block mt-1">{{ $classroom->name }}</span>
            <span class="text-[11px] text-slate-500">{{ $classroom->academic_year }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80">
            <span class="text-[10px] font-bold text-emerald-600 uppercase">Ready to Import</span>
            <span class="text-2xl font-bold text-slate-900 block mt-1">{{ $validCount }}</span>
            <span class="text-[11px] text-slate-500">Valid new students</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80">
            <span class="text-[10px] font-bold text-amber-600 uppercase">Existing / Skipped</span>
            <span class="text-2xl font-bold text-slate-900 block mt-1">{{ $skipCount }}</span>
            <span class="text-[11px] text-slate-500">Already in system</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80">
            <span class="text-[10px] font-bold text-rose-600 uppercase">Errors</span>
            <span class="text-2xl font-bold text-slate-900 block mt-1">{{ $errorCount }}</span>
            <span class="text-[11px] text-slate-500">Invalid records</span>
        </div>
    </div>

    <!-- Rows Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Validated CSV Rows</h3>
            <span class="text-xs text-slate-500">Total {{ count($rows) }} rows parsed</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">#</th>
                        <th class="px-5 py-3">Student Code</th>
                        <th class="px-5 py-3">Full Name</th>
                        <th class="px-5 py-3">Gender</th>
                        <th class="px-5 py-3">Phone</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Message</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($rows as $row)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-5 py-3 font-mono text-slate-400">{{ $row['row_number'] }}</td>
                            <td class="px-5 py-3 font-mono font-bold text-slate-900">{{ $row['student_code'] }}</td>
                            <td class="px-5 py-3 font-medium text-slate-900">{{ $row['full_name'] }}</td>
                            <td class="px-5 py-3">{{ $row['gender'] ?? '-' }}</td>
                            <td class="px-5 py-3">{{ $row['phone'] ?? '-' }}</td>
                            <td class="px-5 py-3">
                                @if ($row['status'] === 'valid')
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700">Valid</span>
                                @elseif ($row['status'] === 'skipped_existing')
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-700">Skip Existing</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-700">Error</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-slate-500 text-[11px]">{{ $row['message'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-5 border-t border-slate-100 flex items-center justify-between">
            <a href="{{ route('students.import') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                &larr; Re-upload File
            </a>

            @if ($validCount > 0)
                <form method="POST" action="{{ route('students.import.commit') }}">
                    @csrf
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow transition cursor-pointer">
                        Confirm & Commit {{ $validCount }} Student(s)
                    </button>
                </form>
            @else
                <button type="button" disabled class="px-6 py-2.5 bg-slate-200 text-slate-400 rounded-xl text-xs font-bold cursor-not-allowed">
                    No Valid Records to Commit
                </button>
            @endif
        </div>
    </div>
</div>
@endsection
