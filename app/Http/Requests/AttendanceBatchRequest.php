<?php

namespace App\Http\Requests;

use App\Models\AttendanceRecord;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttendanceBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'records' => ['required', 'array'],
            'records.*.student_id' => ['required', 'exists:students,id'],
            'records.*.status' => ['required', Rule::in(AttendanceRecord::STATUSES)],
            'records.*.remark' => ['nullable', 'string', 'max:500'],
        ];
    }
}
