<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('student')?->id;

        return [
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'student_code' => ['required', 'string', 'max:50', Rule::unique('students', 'student_code')->ignore($studentId)],
            'full_name' => ['required', 'string', 'max:150'],
            'gender' => ['nullable', 'in:Male,Female,Other'],
            'phone' => ['nullable', 'string', 'max:30'],
        ];
    }
}
