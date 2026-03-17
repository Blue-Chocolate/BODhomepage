<?php

namespace App\Http\Requests\ContactUs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactUsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'email'   => ['nullable', 'email', 'max:255'],
            'subject' => ['required', Rule::in(['General Inquiry', 'Support', 'Feedback', 'Other'])],
            'message' => ['required', 'string'],
            'reply'   => ['nullable', 'string'],
        ];
    }
}