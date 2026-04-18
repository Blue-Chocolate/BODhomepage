<?php

namespace App\Services\Compliance;

use App\Models\Compliance\AssessmentAxis;
use App\Models\Compliance\AssessmentSubmission;
use App\Models\Compliance\SubmissionAnswer;
use App\Models\Compliance\SubmissionRecommendation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ComplianceSubmissionService
{
    // ─── Submission Lifecycle ─────────────────────────────────────────────────────

    public function initiate(int $assessmentId, int $organizationId, ?int $userId = null): AssessmentSubmission
    {
        return AssessmentSubmission::firstOrCreate(
            [
                'assessment_id'   => $assessmentId,
                'organization_id' => $organizationId,
            ],
            [
                'evaluated_by' => $userId,
                'status'       => 'draft',
            ]
        );
    }

    /**
     * Save answers for a SINGLE AXIS only (5 questions at a time).
     * Recalculates axis score and overall score after saving.
     *
     * $answers = [
     *   ['question_id' => int, 'score' => int, 'notes' => ?string],
     *   ...
     * ]
     */
    public function saveAxisAnswers(
        AssessmentSubmission $submission,
        AssessmentAxis $axis,
        array $answers
    ): array {
        $this->guardEditable($submission);

        DB::transaction(function () use ($submission, $answers) {
            foreach ($answers as $answer) {
                SubmissionAnswer::updateOrCreate(
                    [
                        'assessment_submission_id' => $submission->id,
                        'assessment_question_id'   => $answer['question_id'],
                    ],
                    [
                        'score' => $answer['score'],
                        'notes' => $answer['notes'] ?? null,
                    ]
                );
            }

            $submission->recalculateScore();
        });

        $submission->refresh();

        return [
            'axis_score'    => $this->calcAxisScore($submission, $axis),
            'overall_score' => $submission->overall_score,
            'completion'    => $this->getCompletionStatus($submission),
        ];
    }

    // ─── Scoring Helpers ──────────────────────────────────────────────────────────

    /**
     * Calculate score for a specific axis only.
     */
    public function calcAxisScore(AssessmentSubmission $submission, AssessmentAxis $axis): ?float
    {
        $axis->load('questions');

        $questionIds = $axis->questions->pluck('id');

        $scores = SubmissionAnswer::query()
            ->where('assessment_submission_id', $submission->id)
            ->whereIn('assessment_question_id', $questionIds)
            ->where('score', '>', 0)    // exclude 0 = "not applicable"
            ->pluck('score');

        return $scores->isNotEmpty()
            ? round($scores->average(), 2)
            : null;
    }

    /**
     * Returns completion status per axis — used for progress tracking.
     *
     * Returns:
     * [
     *   'total_questions'    => int,
     *   'answered_questions' => int,
     *   'percentage'         => float,
     *   'axes' => [
     *     ['axis_id' => int, 'title' => string, 'answered' => int, 'total' => int, 'complete' => bool, 'axis_score' => ?float],
     *     ...
     *   ]
     * ]
     */
    public function getCompletionStatus(AssessmentSubmission $submission): array
    {
        $submission->load([
            'assessment.axes.questions',
            'answers',
        ]);

        $answeredIds    = $submission->answers->pluck('assessment_question_id')->toArray();
        $totalQuestions = 0;
        $totalAnswered  = 0;
        $axesStatus     = [];

        foreach ($submission->assessment->axes as $axis) {
            $axisQuestionIds = $axis->questions->pluck('id')->toArray();
            $axisAnswered    = count(array_intersect($axisQuestionIds, $answeredIds));
            $axisTotal       = count($axisQuestionIds);

            $totalQuestions += $axisTotal;
            $totalAnswered  += $axisAnswered;

            $axesStatus[] = [
                'axis_id'    => $axis->id,
                'title'      => $axis->title,
                'order'      => $axis->order,
                'answered'   => $axisAnswered,
                'total'      => $axisTotal,
                'complete'   => $axisAnswered === $axisTotal,
                'axis_score' => $this->calcAxisScore($submission, $axis),
            ];
        }

        return [
            'total_questions'    => $totalQuestions,
            'answered_questions' => $totalAnswered,
            'percentage'         => $totalQuestions > 0
                ? round(($totalAnswered / $totalQuestions) * 100, 1)
                : 0.0,
            'axes' => $axesStatus,
        ];
    }

    // ─── Lifecycle Actions ────────────────────────────────────────────────────────

    public function submit(AssessmentSubmission $submission): AssessmentSubmission
    {
        $this->guardEditable($submission);
        $this->guardAllAnswered($submission);

        $submission->update([
            'status'       => 'submitted',
            'submitted_at' => now(),
        ]);

        return $submission->fresh();
    }

    public function review(AssessmentSubmission $submission, string $notes): AssessmentSubmission
    {
        $submission->update([
            'status'          => 'reviewed',
            'evaluator_notes' => $notes,
            'reviewed_at'     => now(),
        ]);

        return $submission->fresh();
    }

    public function decide(AssessmentSubmission $submission, array $data): AssessmentSubmission
    {
        $submission->update([
            'status'              => 'approved',
            'management_decision' => $data['decision'] ?? null,
            'management_action'   => $data['action'],
            'reassess_months'     => $data['reassess_months'] ?? null,
        ]);

        return $submission->fresh();
    }

    // ─── Recommendations ─────────────────────────────────────────────────────────

    public function syncRecommendations(AssessmentSubmission $submission, array $recommendations): AssessmentSubmission
    {
        DB::transaction(function () use ($submission, $recommendations) {
            $submission->recommendations()->delete();

            foreach ($recommendations as $rec) {
                SubmissionRecommendation::create([
                    'assessment_submission_id' => $submission->id,
                    'priority'                 => $rec['priority'],
                    'recommendation'           => $rec['recommendation'],
                    'responsible_party'        => $rec['responsible_party'] ?? null,
                ]);
            }
        });

        return $submission->fresh(['recommendations']);
    }

    // ─── Full Result Payload ─────────────────────────────────────────────────────

    public function buildResult(AssessmentSubmission $submission): array
    {
        $submission->load([
            'assessment',
            'organization',
            'evaluator',
            'answers',
            'recommendations',
            'assessment.axes.questions',
        ]);

        return [
            'submission' => [
                'id'                  => $submission->id,
                'status'              => $submission->status,
                'overall_score'       => $submission->overall_score,
                'compliance_level'    => $submission->compliance_level,
                'submitted_at'        => $submission->submitted_at,
                'reviewed_at'         => $submission->reviewed_at,
                'evaluator_notes'     => $submission->evaluator_notes,
                'management_action'   => $submission->management_action,
                'management_decision' => $submission->management_decision,
                'reassess_months'     => $submission->reassess_months,
            ],
            'assessment' => [
                'id'          => $submission->assessment->id,
                'title'       => $submission->assessment->title,
                'period_year' => $submission->assessment->period_year,
            ],
            'organization' => [
                'id'   => $submission->organization->id,
                'name' => $submission->organization->name,
                'type' => $submission->organization->type,
            ],
            'completion'      => $this->getCompletionStatus($submission),
            'axis_breakdown'  => $submission->getAxisBreakdown(),
            'recommendations' => $submission->recommendations->map(fn ($r) => [
                'priority'          => $r->priority,
                'recommendation'    => $r->recommendation,
                'responsible_party' => $r->responsible_party,
            ])->values()->toArray(),
        ];
    }

    // ─── Guards ───────────────────────────────────────────────────────────────────

    private function guardEditable(AssessmentSubmission $submission): void
    {
        if (! in_array($submission->status, ['draft', 'reviewed'])) {
            throw ValidationException::withMessages([
                'status' => 'لا يمكن تعديل تقييم في حالة "' . $submission->status . '"',
            ]);
        }
    }

    private function guardAllAnswered(AssessmentSubmission $submission): void
    {
        $submission->load('assessment.axes.questions', 'answers');

        $totalQuestions = $submission->assessment->axes
            ->flatMap(fn ($a) => $a->questions)
            ->count();

        $answeredCount = $submission->answers->count();

        if ($answeredCount < $totalQuestions) {
            $missing = $totalQuestions - $answeredCount;
            throw ValidationException::withMessages([
                'answers' => "يوجد {$missing} سؤال لم يتم الإجابة عليه بعد.",
            ]);
        }
    }
}