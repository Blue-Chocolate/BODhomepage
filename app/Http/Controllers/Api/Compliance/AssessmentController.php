<?php

namespace App\Http\Controllers\Api\Compliance;

use App\Http\Controllers\Controller;
use App\Models\Compliance\Assessment;
use App\Models\Compliance\AssessmentAxis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    /**
     * GET /api/compliance/assessments
     * List active assessments.
     */
    public function index(): JsonResponse
    {
        $assessments = Assessment::active()
            ->orderByDesc('period_year')
            ->get(['id', 'title', 'description', 'period_year', 'opens_at', 'closes_at']);

        return response()->json(['data' => $assessments]);
    }

   
    /**
 * GET /api/compliance/axes/{axis}/questions
 */
public function axisQuestions(AssessmentAxis $axis): JsonResponse
{
    $axis->load(['questions' => fn ($q) => $q->where('is_active', true)->orderBy('order')]);

    return response()->json([
        'data' => [
            'axis_id'                 => $axis->id,
            'title'                   => $axis->title,
            'description'             => $axis->description,
            'recommendation_platform' => $axis->recommendation_platform,
            'order'                   => $axis->order,
            'questions'               => $axis->questions->map(fn ($q) => [
                'id'       => $q->id,
                'title'    => $q->title,
                'guidance' => $q->guidance,
                'weight'   => $q->weight,
                'order'    => $q->order,
            ]),
        ],
    ]);
}

    /**
     * GET /api/compliance/assessments/{assessment}
     * Full structure: axes + questions (for rendering the form).
     */
    public function show(Assessment $assessment): JsonResponse
    {
        $assessment->load([
            'axes' => fn ($q) => $q->where('is_active', true)->orderBy('order'),
            'axes.questions' => fn ($q) => $q->where('is_active', true)->orderBy('order'),
        ]);

        return response()->json([
            'data' => [
                'id'          => $assessment->id,
                'title'       => $assessment->title,
                'description' => $assessment->description,
                'period_year' => $assessment->period_year,
                'opens_at'    => $assessment->opens_at,
                'closes_at'   => $assessment->closes_at,
                'axes'        => $assessment->axes->map(fn ($axis) => [
                    'id'                      => $axis->id,
                    'title'                   => $axis->title,
                    'description'             => $axis->description,
                    'recommendation_platform' => $axis->recommendation_platform,
                    'order'                   => $axis->order,
                    'questions'               => $axis->questions->map(fn ($q) => [
                        'id'       => $q->id,
                        'title'    => $q->title,
                        'guidance' => $q->guidance,
                        'order'    => $q->order,
                    ]),
                ]),
            ],
        ]);
    }
}