<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subjectId = $this->route('subject')?->id;

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('subjects', 'code')->ignore($subjectId)],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
