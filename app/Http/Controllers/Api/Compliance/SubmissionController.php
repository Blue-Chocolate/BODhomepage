<?php

namespace App\Http\Controllers\Api\Compliance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compliance\InitiateSubmissionRequest;
use App\Http\Requests\Compliance\SaveAxisAnswersRequest;
use App\Http\Requests\Compliance\ManagementDecisionRequest;
use App\Http\Requests\Compliance\SyncRecommendationsRequest;
use App\Models\Compliance\AssessmentAxis;
use App\Models\Compliance\AssessmentSubmission;
use App\Services\Compliance\ComplianceSubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function __construct(
        private readonly ComplianceSubmissionService $service
    ) {}

    /**
     * GET /api/compliance/submissions
     */
    public function index(Request $request): JsonResponse
    {
        $submissions = AssessmentSubmission::query()
            ->with(['assessment:id,title,period_year', 'organization:id,name,type'])
            ->when($request->assessment_id,   fn ($q) => $q->where('assessment_id',   $request->assessment_id))
            ->when($request->organization_id, fn ($q) => $q->where('organization_id', $request->organization_id))
            ->when($request->status,          fn ($q) => $q->where('status',          $request->status))
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json($submissions);
    }

  /**
 * POST /api/compliance/submissions/initiate
 */
public function initiate(InitiateSubmissionRequest $request): JsonResponse
{
    $organization = auth()->user(); // هو الـ Organization نفسه

    $submission = $this->service->initiate(
        $request->assessment_id,
        $organization->id,
    );

    return response()->json([
        'message' => 'تم إنشاء التقييم أو استرجاعه بنجاح',
        'data'    => $this->service->buildResult($submission),
    ], 201);
}

    /**
     * GET /api/compliance/submissions/{submission}
     */
    public function show(AssessmentSubmission $submission): JsonResponse
    {
        return response()->json([
            'data' => $this->service->buildResult($submission),
        ]);
    }

    /**
     * POST /api/compliance/submissions/{submission}/axes/{axis}/answers
     *
     * Save answers for ONE axis at a time.
     *
     * Request body:
     * {
     *   "answers": [
     *     { "question_id": 1, "score": 4, "notes": "ملاحظة" },
     *     { "question_id": 2, "score": 3 },
     *     { "question_id": 3, "score": 5 },
     *     { "question_id": 4, "score": 2 },
     *     { "question_id": 5, "score": 0 }  // 0 = غير مطبق
     *   ]
     * }
     *
     * Response includes:
     * - axis_score    (average for this axis)
     * - overall_score (updated average of all axes so far)
     * - completion    (per-axis progress tracker)
     */
    public function saveAxisAnswers(
        SaveAxisAnswersRequest $request,
        AssessmentSubmission $submission,
        AssessmentAxis $axis
    ): JsonResponse {
        $result = $this->service->saveAxisAnswers(
            $submission,
            $axis,
            $request->validated('answers')
        );

        return response()->json([
            'message'       => "تم حفظ إجابات محور \"{$axis->title}\" بنجاح",
            'axis_score'    => $result['axis_score'],
            'overall_score' => $result['overall_score'],
            'completion'    => $result['completion'],
        ]);
    }

    /**
     * POST /api/compliance/submissions/{submission}/submit
     */
    public function submit(AssessmentSubmission $submission): JsonResponse
    {
        $submission = $this->service->submit($submission);

        return response()->json([
            'message' => 'تم تقديم التقييم بنجاح',
            'data'    => $this->service->buildResult($submission),
        ]);
    }

    /**
     * POST /api/compliance/submissions/{submission}/review
     */
    public function review(Request $request, AssessmentSubmission $submission): JsonResponse
    {
        $request->validate(['notes' => 'required|string']);

        $submission = $this->service->review($submission, $request->notes);

        return response()->json([
            'message' => 'تم تسجيل مراجعة المقيّم',
            'data'    => $this->service->buildResult($submission),
        ]);
    }

    /**
     * POST /api/compliance/submissions/{submission}/decide
     */
    public function decide(ManagementDecisionRequest $request, AssessmentSubmission $submission): JsonResponse
    {
        $submission = $this->service->decide($submission, $request->validated());

        return response()->json([
            'message' => 'تم تسجيل قرار الإدارة',
            'data'    => $this->service->buildResult($submission),
        ]);
    }

    /**
     * POST /api/compliance/submissions/{submission}/recommendations
     */
    public function syncRecommendations(
        SyncRecommendationsRequest $request,
        AssessmentSubmission $submission
    ): JsonResponse {
        $submission = $this->service->syncRecommendations(
            $submission,
            $request->validated('recommendations')
        );

        return response()->json([
            'message' => 'تم حفظ التوصيات',
            'data'    => $this->service->buildResult($submission),
        ]);
    }
}