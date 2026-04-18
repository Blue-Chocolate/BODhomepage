<?php
// ════════════════════════════════════════════════════════════════
// ManagementDecisionRequest.php
// ════════════════════════════════════════════════════════════════
namespace App\Http\Requests\Compliance;

use Illuminate\Foundation\Http\FormRequest;

class ManagementDecisionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'action' => [
                'required',
                'string',
                'in:approved,approved_with_plan,urgent_treatment,reassess',
            ],
            'decision'       => ['nullable', 'string', 'max:2000'],
            'reassess_months'=> ['nullable', 'integer', 'min:1', 'max:24',
                                 'required_if:action,reassess'],
        ];
    }

    public function messages(): array
    {
        return [
            'action.in'                    => 'قرار الإدارة غير صالح',
            'reassess_months.required_if'  => 'يجب تحديد عدد الأشهر عند اختيار إعادة التقييم',
        ];
    }
}