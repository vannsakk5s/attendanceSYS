<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateViewLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->is_active;
    }

    public function rules(): array
    {
        return [
            'expiry_date' => ['nullable', 'date', 'after:today'],
        ];
    }
}
