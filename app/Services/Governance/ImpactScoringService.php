<?php

namespace App\Services\Governance;

use App\Models\Governance\ImpactReport;
use App\Models\Governance\ImpactBeneficiaryRecord;
use Illuminate\Support\Facades\DB;

class ImpactScoringService
{
    /**
     * احسب مؤشرات الأثر واحفظها.
     */
    public function calculate(ImpactReport $report): ImpactReport
    {
        // مؤشر الأثر الكلي = (شمول + جودة + مناصرة) / 3
        $overallImpactScore = round(
            ($report->social_inclusion + $report->service_quality + $report->advocacy) / 3,
            2
        );

        // SROI = قيمة الأثر الاجتماعي / قيمة الاستثمار
        $sroi = $report->investment_value > 0
            ? round($report->social_impact_value / $report->investment_value, 4)
            : null;

        // تصنيف الأثر
        $classification = match (true) {
            $overallImpactScore >= 75 => 'أثر ممتاز',
            $overallImpactScore >= 55 => 'أثر جيد',
            default                   => 'يحتاج تحسين',
        };

        $report->update([
            'overall_impact_score'  => $overallImpactScore,
            'sroi'                  => $sroi,
            'impact_classification' => $classification,
            'calculated_at'         => now(),
        ]);

        return $report->fresh(['beneficiaryRecords']);
    }

    /**
     * Sync beneficiary records (dynamic list).
     * $records = [['period' => 'Q1 2025', 'beneficiaries' => 120], ...]
     */
    public function syncBeneficiaryRecords(ImpactReport $report, array $records): ImpactReport
    {
        DB::transaction(function () use ($report, $records) {
            $report->beneficiaryRecords()->delete();

            foreach ($records as $record) {
                ImpactBeneficiaryRecord::create([
                    'impact_report_id' => $report->id,
                    'period'           => $record['period'],
                    'beneficiaries'    => $record['beneficiaries'],
                ]);
            }
        });

        return $report->fresh(['beneficiaryRecords']);
    }

    /**
     * Full result payload for impact report.
     */
    public function buildResult(ImpactReport $report): array
    {
        $report->load(['program.organization', 'beneficiaryRecords']);

        $totalBeneficiaries = $report->total_beneficiaries;
        $classificationData = $report->classification_data;

        return [
            'report' => [
                'id'                    => $report->id,
                'year'                  => $report->year,
                'social_inclusion'      => $report->social_inclusion,
                'service_quality'       => $report->service_quality,
                'advocacy'              => $report->advocacy,
                'investment_value'      => $report->investment_value,
                'social_impact_value'   => $report->social_impact_value,
                'overall_impact_score'  => $report->overall_impact_score,
                'sroi'                  => $report->sroi,
                'sroi_display'          => $report->sroi ? '1 : ' . number_format($report->sroi, 1) : null,
                'impact_classification' => $report->impact_classification,
                'classification_color'  => $classificationData['color'] ?? null,
                'classification_icon'   => $classificationData['icon'] ?? null,
                'total_beneficiaries'   => $totalBeneficiaries,
                'calculated_at'         => $report->calculated_at,
            ],
            'program' => [
                'id'   => $report->program->id,
                'name' => $report->program->name,
            ],
            'organization' => [
                'id'   => $report->program->organization->id,
                'name' => $report->program->organization->name,
            ],
            'beneficiary_records' => $report->beneficiaryRecords->map(fn ($r) => [
                'period'        => $r->period,
                'beneficiaries' => $r->beneficiaries,
            ])->values()->toArray(),
            'gauges' => [
                ['label' => 'الشمول الاجتماعي', 'value' => $report->social_inclusion, 'color' => '#8B5CF6'],
                ['label' => 'جودة الخدمة',      'value' => $report->service_quality,  'color' => '#F59E0B'],
                ['label' => 'المناصرة',          'value' => $report->advocacy,         'color' => '#EF4444'],
            ],
        ];
    }
}