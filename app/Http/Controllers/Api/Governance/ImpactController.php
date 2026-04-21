<?php

namespace App\Http\Controllers\Api\Governance;

use App\Http\Controllers\Controller;
use App\Models\Governance\ImpactReport;
use App\Models\Governance\Program;
use App\Services\Governance\ImpactScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImpactController extends Controller
{
    public function __construct(
        private readonly ImpactScoringService $service
    ) {}

    /**
     * POST /api/governance/programs/{program}/impact
     * إنشاء أو تحديث تقرير الأثر
     */
    public function store(Request $request, Program $program): JsonResponse
    {
        $this->authorizeProgram($program);

        $data = $request->validate([
            'year'                  => ['required', 'integer', 'min:2020', 'max:2030'],
            'social_inclusion'      => ['required', 'numeric', 'min:0', 'max:100'],
            'service_quality'       => ['required', 'numeric', 'min:0', 'max:100'],
            'advocacy'              => ['required', 'numeric', 'min:0', 'max:100'],
            'investment_value'      => ['required', 'numeric', 'min:0'],
            'social_impact_value'   => ['required', 'numeric', 'min:0'],
            // سجلات المستفيدين عبر الزمن (اختيارية)
            'beneficiary_records'               => ['nullable', 'array'],
            'beneficiary_records.*.period'      => ['required', 'string'],
            'beneficiary_records.*.beneficiaries' => ['required', 'integer', 'min:0'],
        ]);

        $report = ImpactReport::updateOrCreate(
            ['program_id' => $program->id, 'year' => $data['year']],
            [
                'social_inclusion'    => $data['social_inclusion'],
                'service_quality'     => $data['service_quality'],
                'advocacy'            => $data['advocacy'],
                'investment_value'    => $data['investment_value'],
                'social_impact_value' => $data['social_impact_value'],
            ]
        );

        // حفظ سجلات المستفيدين لو موجودة
        if (! empty($data['beneficiary_records'])) {
            $this->service->syncBeneficiaryRecords($report, $data['beneficiary_records']);
        }

        // احسب المؤشرات
        $report = $this->service->calculate($report);

        return response()->json([
            'message' => 'تم حفظ تقرير الأثر وحساب المؤشرات بنجاح',
            'data'    => $this->service->buildResult($report),
        ], 201);
    }

    /**
     * GET /api/governance/programs/{program}/impact
     */
    public function show(Request $request, Program $program): JsonResponse
    {
        $this->authorizeProgram($program);

        $year   = $request->year ?? date('Y');
        $report = ImpactReport::where('program_id', $program->id)
            ->where('year', $year)
            ->with('beneficiaryRecords')
            ->firstOrFail();

        return response()->json([
            'data' => $this->service->buildResult($report),
        ]);
    }

    /**
     * POST /api/governance/programs/{program}/impact/beneficiaries
     * تحديث سجلات المستفيدين فقط
     */
    public function syncBeneficiaries(Request $request, Program $program): JsonResponse
    {
        $this->authorizeProgram($program);

        $data = $request->validate([
            'year'                              => ['required', 'integer'],
            'records'                           => ['required', 'array', 'min:1'],
            'records.*.period'                  => ['required', 'string'],
            'records.*.beneficiaries'           => ['required', 'integer', 'min:0'],
        ]);

        $report = ImpactReport::where('program_id', $program->id)
            ->where('year', $data['year'])
            ->firstOrFail();

        $report = $this->service->syncBeneficiaryRecords($report, $data['records']);

        return response()->json([
            'message' => 'تم تحديث سجلات المستفيدين',
            'data'    => $this->service->buildResult($report),
        ]);
    }

    private function authorizeProgram(Program $program): void
    {
        abort_if($program->organization_id !== auth()->id(), 403, 'غير مصرح');
    }
}