<?php

namespace App\Models\Compliance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Organization\Organization;
class AssessmentSubmission extends Model
{
    use SoftDeletes;

    protected $table = 'assessment_submissions';

    protected $fillable = [
        'assessment_id',
        'organization_id',
        'evaluated_by',
        'status',
        'evaluator_notes',
        'management_decision',
        'management_action',
        'reassess_months',
        'overall_score',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'overall_score'  => 'float',
        'submitted_at'   => 'datetime',
        'reviewed_at'    => 'datetime',
    ];

    // ─── Score Labels ────────────────────────────────────────────────────────────

    public static array $scoreLevels = [
        ['min' => 4.5, 'max' => 5.0,  'label' => 'امتثال مؤسسي ناضج',  'color' => 'success'],
        ['min' => 3.5, 'max' => 4.49, 'label' => 'امتثال جيد',          'color' => 'success'],
        ['min' => 2.5, 'max' => 3.49, 'label' => 'امتثال متوسط',        'color' => 'warning'],
        ['min' => 1.5, 'max' => 2.49, 'label' => 'امتثال ضعيف',         'color' => 'danger'],
        ['min' => 0.0, 'max' => 1.49, 'label' => 'خطر مؤسسي',           'color' => 'danger'],
    ];

    // ─── Relationships ────────────────────────────────────────────────────────────

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SubmissionAnswer::class);
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(SubmissionRecommendation::class);
    }

    // ─── Scoring ─────────────────────────────────────────────────────────────────

    /**
     * Recalculate and persist the overall score.
     * Simple average: avg of axis averages (each axis = avg of its question scores).
     * Score 0 ("not applicable") is excluded from averages.
     */
    public function recalculateScore(): void
    {
        $this->load([
            'assessment.axes.questions',
            'answers',
        ]);

        $answersMap = $this->answers->keyBy('assessment_question_id');
        $axisAverages = [];

        foreach ($this->assessment->axes as $axis) {
            $scores = $axis->questions
                ->map(fn ($q) => $answersMap->get($q->id)?->score ?? null)
                ->filter(fn ($score) => $score !== null && $score > 0); // exclude 0 = N/A

            if ($scores->isNotEmpty()) {
                $axisAverages[] = $scores->average();
            }
        }

        $this->overall_score = count($axisAverages)
            ? round(array_sum($axisAverages) / count($axisAverages), 2)
            : null;

        $this->saveQuietly();
    }

    /**
     * Per-axis score breakdown (used by API and Filament).
     */
    public function getAxisBreakdown(): array
    {
        $this->load([
            'assessment.axes.questions',
            'answers',
        ]);

        $answersMap = $this->answers->keyBy('assessment_question_id');
        $breakdown  = [];

        foreach ($this->assessment->axes as $axis) {
            $questionScores = [];

            foreach ($axis->questions as $question) {
                $answer = $answersMap->get($question->id);
                $questionScores[] = [
                    'question_id' => $question->id,
                    'title'       => $question->title,
                    'score'       => $answer?->score,
                    'notes'       => $answer?->notes,
                ];
            }

            $validScores = collect($questionScores)
                ->pluck('score')
                ->filter(fn ($s) => $s !== null && $s > 0);

            $breakdown[] = [
                'axis_id'                 => $axis->id,
                'title'                   => $axis->title,
                'order'                   => $axis->order,
                'recommendation_platform' => $axis->recommendation_platform,
                'axis_score'              => $validScores->isNotEmpty()
                    ? round($validScores->average(), 2)
                    : null,
                'questions'               => $questionScores,
            ];
        }

        return $breakdown;
    }

    /**
     * Human-readable compliance level based on overall_score.
     */
    public function getComplianceLevelAttribute(): ?array
    {
        if ($this->overall_score === null) {
            return null;
        }

        foreach (static::$scoreLevels as $level) {
            if ($this->overall_score >= $level['min'] && $this->overall_score <= $level['max']) {
                return $level;
            }
        }

        return null;
    }
}