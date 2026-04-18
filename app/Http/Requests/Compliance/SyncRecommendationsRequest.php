<?php

namespace App\Http\Requests\Compliance;

use Illuminate\Foundation\Http\FormRequest;

class SyncRecommendationsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'recommendations'                    => ['required', 'array', 'min:1'],
            'recommendations.*.priority'         => ['required', 'string', 'in:high,medium,low'],
            'recommendations.*.recommendation'   => ['required', 'string', 'max:2000'],
            'recommendations.*.responsible_party'=> ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'recommendations.*.priority.in' => 'الأولوية يجب أن تكون: high أو medium أو low',
        ];
    }
}