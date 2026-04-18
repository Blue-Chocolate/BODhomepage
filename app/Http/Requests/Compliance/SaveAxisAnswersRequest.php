<?php

namespace App\Http\Requests\Compliance;

use App\Models\Compliance\AssessmentAxis;
use App\Models\Compliance\AssessmentSubmission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SaveAxisAnswersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'answers'                => ['required', 'array', 'min:1'],
            'answers.*.question_id'  => ['required', 'integer', 'exists:assessment_questions,id'],
            'answers.*.score'        => ['required', 'integer', 'min:0', 'max:5'],
            'answers.*.notes'        => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Extra validation: all question_ids must belong to this axis.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            /** @var AssessmentAxis $axis */
            $axis = $this->route('axis');

            /** @var AssessmentSubmission $submission */
            $submission = $this->route('submission');

            // Guard: axis must belong to the submission's assessment
            if ($axis->assessment_id !== $submission->assessment_id) {
                $validator->errors()->add('axis', 'هذا المحور لا ينتمي للتقييم المحدد');
                return;
            }

            $validQuestionIds = $axis->questions()->pluck('id')->toArray();
            $sentQuestionIds  = collect($this->input('answers', []))->pluck('question_id')->toArray();

            $invalid = array_diff($sentQuestionIds, $validQuestionIds);

            if (! empty($invalid)) {
                $validator->errors()->add(
                    'answers',
                    'الأسئلة التالية لا تنتمي لهذا المحور: ' . implode(', ', $invalid)
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'answers.required'              => 'يجب إرسال الإجابات',
            'answers.*.question_id.exists'  => 'السؤال غير موجود',
            'answers.*.score.min'           => 'الحد الأدنى للدرجة هو 0 (غير مطبق)',
            'answers.*.score.max'           => 'الحد الأقصى للدرجة هو 5',
        ];
    }
}