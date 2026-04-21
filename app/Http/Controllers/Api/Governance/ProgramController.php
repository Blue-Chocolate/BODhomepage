<?php

namespace App\Http\Controllers\Api\Governance;

use App\Http\Controllers\Controller;
use App\Models\Governance\Program;
use App\Models\Governance\ProgramQuarter;
use App\Services\Governance\GovernanceScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function __construct(
        private readonly GovernanceScoringService $scoringService
    ) {}

    /**
     * GET /api/governance/programs
     */
    public function index(Request $request): JsonResponse
    {
        $programs = Program::query()
            ->where('organization_id', auth()->id())
            ->with(['governanceScore', 'quarters'])
            ->when($request->year, fn ($q) =>
                $q->whereHas('quarters', fn ($q2) => $q2->where('year', $request->year))
            )
            ->when($request->has('active'), fn ($q) =>
                $q->where('is_active', $request->boolean('active'))
            )
            ->get();

        return response()->json(['data' => $programs]);
    }

    /**
     * POST /api/governance/programs
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'status'             => ['required', 'in:planning,in_progress,completed,suspended'],
            'total_actual_cost'  => ['required', 'numeric', 'min:0'],
            'execution_duration' => ['nullable', 'integer', 'min:1'],
            'start_date'         => ['nullable', 'date'],
            'end_date'           => ['nullable', 'date', 'after_or_equal:start_date'],
            'resource_efficiency'=> ['required', 'numeric', 'min:0', 'max:100'],
            'is_active'          => ['sometimes', 'boolean'],
        ]);

        $program = Program::create([
            ...$data,
            'organization_id'      => auth()->id(),
            'cost_per_beneficiary' => 0, // placeholder until scoring runs
        ]);

        return response()->json([
            'message' => 'تم إنشاء البرنامج بنجاح',
            'data'    => $program,
        ], 201);
    }

    /**
     * GET /api/governance/programs/{program}
     */
    public function show(Program $program): JsonResponse
    {
        $this->authorizeProgram($program);

        $program->load(['quarters', 'governanceScore', 'impactReport.beneficiaryRecords']);

        return response()->json(['data' => $program]);
    }

    /**
     * PUT /api/governance/programs/{program}
     */
    public function update(Request $request, Program $program): JsonResponse
    {
        $this->authorizeProgram($program);

        $data = $request->validate([
            'name'               => ['sometimes', 'string', 'max:255'],
            'status'             => ['sometimes', 'in:planning,in_progress,completed,suspended'],
            'total_actual_cost'  => ['sometimes', 'numeric', 'min:0'],
            'execution_duration' => ['nullable', 'integer', 'min:1'],
            'start_date'         => ['nullable', 'date'],
            'end_date'           => ['nullable', 'date', 'after_or_equal:start_date'],
            'resource_efficiency'=> ['sometimes', 'numeric', 'min:0', 'max:100'],
            'is_active'          => ['sometimes', 'boolean'],
        ]);

        $program->update($data);

        $shouldRecalculate = isset($data['status'])
            || isset($data['resource_efficiency'])
            || isset($data['execution_duration'])
            || isset($data['start_date'])      // affects timeline KPIs
            || isset($data['end_date']);

        if ($shouldRecalculate) {
            $year = $program->quarters()->max('year') ?? date('Y');
            $this->scoringService->calculate($program, $year);
        }

        return response()->json([
            'message' => 'تم تحديث البرنامج بنجاح',
            'data'    => $program->fresh(['governanceScore']),
        ]);
    }

    /**
     * POST /api/governance/programs/{program}/quarters
     * إضافة أو تحديث بيانات ربع
     */
    public function saveQuarter(Request $request, Program $program): JsonResponse
    {
        $this->authorizeProgram($program);

        $data = $request->validate([
            'quarter'       => ['required', 'in:Q1,Q2,Q3,Q4'],
            'year'          => ['required', 'integer', 'min:2020', 'max:2099'],
            'budget'        => ['required', 'numeric', 'min:0'],
            'actual_cost'   => ['required', 'numeric', 'min:0'],
            'beneficiaries' => ['required', 'integer', 'min:0'],
        ]);

        $quarter = ProgramQuarter::updateOrCreate(
            ['program_id' => $program->id, 'quarter' => $data['quarter'], 'year' => $data['year']],
            ['budget' => $data['budget'], 'actual_cost' => $data['actual_cost'], 'beneficiaries' => $data['beneficiaries']]
        );

        $score = $this->scoringService->calculate($program, $data['year']);

        return response()->json([
            'message'    => "تم حفظ بيانات {$data['quarter']} بنجاح",
            'quarter'    => $quarter,
            'score'      => $this->buildScorePayload($score, $program),
        ]);
    }

    /**
     * GET /api/governance/programs/{program}/score
     */
    public function score(Request $request, Program $program): JsonResponse
    {
        $this->authorizeProgram($program);

        $year  = $request->year ?? date('Y');
        $score = $this->scoringService->calculate($program, $year);

        return response()->json([
            'data' => $this->buildScorePayload($score, $program),
        ]);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    private function authorizeProgram(Program $program): void
    {
        abort_if($program->organization_id !== auth()->id(), 403, 'غير مصرح');
    }

    private function buildScorePayload($score, Program $program): array
    {
        return [
            'overall_score'        => $score->overall_score,
            'classification'       => $score->classification['label'] ?? null,
            'classification_color' => $score->classification['color'] ?? null,
            'pillars'              => $score->pillars,
            'kpis' => [
                'total_budget'         => $score->total_budget,
                'total_beneficiaries'  => $score->total_beneficiaries,
                'budget_variance'      => $score->budget_variance,
                'cost_per_beneficiary' => $score->cost_per_beneficiary,
            ],
            'program_dates' => [
                'start_date'         => $program->start_date,
                'end_date'           => $program->end_date,
                'execution_duration' => $program->execution_duration,
                'is_active'          => $program->is_active,
            ],
            'quarters' => $program->quarters()
                ->where('year', $score->year)
                ->get()
                ->map(fn ($q) => [
                    'quarter'         => $q->quarter,
                    'year'            => $q->year,
                    'budget'          => $q->budget,
                    'actual_cost'     => $q->actual_cost,
                    'beneficiaries'   => $q->beneficiaries,
                    'budget_variance' => $q->budget_variance,
                ]),
            'calculated_at' => $score->calculated_at,
        ];
    }
}