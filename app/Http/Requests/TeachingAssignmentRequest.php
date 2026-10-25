<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeachingAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'semester' => ['required', 'string', 'max:50'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ];
    }
}
