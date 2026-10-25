<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassroomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['nullable', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:150'],
            'academic_year' => ['required', 'string', 'max:50'],
            'shift' => ['nullable', 'string', 'max:50'],
        ];
    }
}
