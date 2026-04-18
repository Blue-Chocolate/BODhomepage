<?php

namespace App\Models\Compliance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionAnswer extends Model
{
    protected $table = 'submission_answers';

    protected $fillable = [
        'assessment_submission_id',
        'assessment_question_id',
        'score',
        'notes',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AssessmentSubmission::class, 'assessment_submission_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(AssessmentQuestion::class, 'assessment_question_id');
    }

    public function getScoreLabelAttribute(): string
    {
        return match ($this->score) {
            5 => 'امتثال كامل ومستدام',
            4 => 'امتثال جيد مع تحسينات بسيطة',
            3 => 'امتثال جزئي',
            2 => 'ضعف امتثال',
            1 => 'عدم امتثال',
            0 => 'غير مطبق',
            default => 'غير محدد',
        };
    }
}