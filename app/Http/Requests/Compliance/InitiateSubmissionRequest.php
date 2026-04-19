<?php
// ════════════════════════════════════════════════════════════════
// InitiateSubmissionRequest.php
// ════════════════════════════════════════════════════════════════
namespace App\Http\Requests\Compliance;

use Illuminate\Foundation\Http\FormRequest;

class InitiateSubmissionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'assessment_id'   => ['required', 'integer', 'exists:assessments,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'assessment_id.exists'   => 'التقييم غير موجود',
            'organization_id.exists' => 'الجهة غير موجودة',
        ];
    }
}