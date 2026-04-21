<?php

namespace App\Services\Governance;

use App\Models\Governance\Program;
use App\Models\Governance\ProgramGovernanceScore;
use App\Models\Governance\ProgramQuarter;
use Illuminate\Support\Collection;

class GovernanceScoringService
{
    /**
     * احسب مؤشر الحوكمة الكامل لبرنامج في سنة معينة واحفظه.
     */
    public function calculate(Program $program, int $year): ProgramGovernanceScore
    {
        $quarters = $program->quarters()
            ->where('year', $year)
            ->get();

        // ─── المؤشرات المحتسبة التلقائية ─────────────────────────────────────────
        $totalBudget       = $quarters->sum('budget');
        $totalActualCost   = $quarters->sum('actual_cost');
        $totalBeneficiaries = $quarters->sum('beneficiaries');
        $quartersCount     = $quarters->count();

        $budgetVariance = $totalBudget > 0
            ? round((($totalBudget - $totalActualCost) / $totalBudget) * 100, 2)
            : 0;

        $costPerBeneficiary = $totalBeneficiaries > 0
            ? round($totalActualCost / $totalBeneficiaries, 2)
            : null;

        // ─── الركائز الست ────────────────────────────────────────────────────────

        $pillarImpact        = $this->calcPillarImpact($totalBeneficiaries, $costPerBeneficiary, $program);
        $pillarIntegrity     = $this->calcPillarIntegrity($program->resource_efficiency, $budgetVariance);
        $pillarEmpowerment   = $this->calcPillarEmpowerment($totalBeneficiaries, $totalBudget);
        $pillarInnovation    = $this->calcPillarInnovation($program->status);
        $pillarCapacity      = $this->calcPillarCapacity($totalBudget, $totalActualCost, $program->resource_efficiency);
        $pillarSustainability = $this->calcPillarSustainability($quartersCount, $program->execution_duration);

        // ─── المتوسط المرجح النهائي ───────────────────────────────────────────────
        $overallScore = round(
            ($pillarImpact + $pillarIntegrity + $pillarEmpowerment +
             $pillarInnovation + $pillarCapacity + $pillarSustainability) / 6,
            2
        );

        // ─── حفظ أو تحديث ────────────────────────────────────────────────────────
        $score = ProgramGovernanceScore::updateOrCreate(
            ['program_id' => $program->id, 'year' => $year],
            [
                'pillar_impact'          => $pillarImpact,
                'pillar_integrity'       => $pillarIntegrity,
                'pillar_empowerment'     => $pillarEmpowerment,
                'pillar_innovation'      => $pillarInnovation,
                'pillar_capacity'        => $pillarCapacity,
                'pillar_sustainability'  => $pillarSustainability,
                'overall_score'          => $overallScore,
                'total_budget'           => $totalBudget,
                'total_beneficiaries'    => $totalBeneficiaries,
                'budget_variance'        => $budgetVariance,
                'cost_per_beneficiary'   => $costPerBeneficiary,
                'calculated_at'          => now(),
            ]
        );

        return $score->fresh();
    }

    // ─── الركيزة الأولى: الغاية والأثر ──────────────────────────────────────────
    // يعتمد على عدد المستفيدين (60%) وتكلفة المستفيد (40%)

    private function calcPillarImpact(int $totalBeneficiaries, ?float $costPerBeneficiary, Program $program): float
    {
        // عدد المستفيدين: كلما زاد أفضل — نُعيّر على 1000 مستفيد كحد أقصى مرجعي
        $beneficiaryScore = min(($totalBeneficiaries / 1000) * 100, 100);

        // تكلفة المستفيد: كلما قلت أفضل — مرجع: 500 ريال تكلفة مثالية
        $costScore = 0;
        if ($costPerBeneficiary !== null && $costPerBeneficiary > 0) {
            $costScore = min((500 / $costPerBeneficiary) * 100, 100);
        }

        return round(($beneficiaryScore * 0.60) + ($costScore * 0.40), 2);
    }

    // ─── الركيزة الثانية: النزاهة المالية ───────────────────────────────────────
    // كفاءة الموارد (60%) + الالتزام بالميزانية (40%)

    private function calcPillarIntegrity(?float $resourceEfficiency, float $budgetVariance): float
    {
        $resourceScore = min($resourceEfficiency ?? 0, 100);

        // الالتزام بالميزانية: انحراف أقل = أفضل
        // إذا الانحراف إيجابي (وفّرنا) هذا ممتاز، إذا سالب (تجاوزنا) هذا سيء
        $budgetComplianceScore = match (true) {
            $budgetVariance >= 10  => 100,   // وفّرنا 10%+ ممتاز
            $budgetVariance >= 5   => 85,
            $budgetVariance >= 0   => 70,    // مطابق تقريباً
            $budgetVariance >= -5  => 50,
            $budgetVariance >= -10 => 30,
            default                => 10,    // تجاوز كبير
        };

        return round(($resourceScore * 0.60) + ($budgetComplianceScore * 0.40), 2);
    }

    // ─── الركيزة الثالثة: التمكين المجتمعي ──────────────────────────────────────
    // نسبة المستفيدين لكل 10,000 ريال ميزانية

    private function calcPillarEmpowerment(int $totalBeneficiaries, float $totalBudget): float
    {
        if ($totalBudget <= 0) {
            return 0;
        }

        // المستفيدون لكل 10,000 ريال — مرجع: 10 مستفيدين لكل 10,000 ريال = 100%
        $ratio = ($totalBeneficiaries / $totalBudget) * 10000;
        return round(min(($ratio / 10) * 100, 100), 2);
    }

    // ─── الركيزة الرابعة: الابتكار والتكيف ──────────────────────────────────────
    // مباشرة من حالة المشروع

    private function calcPillarInnovation(string $status): float
    {
        return (float) Program::$statusScores[$status] ?? 0;
    }

    // ─── الركيزة الخامسة: بناء القدرات ──────────────────────────────────────────
    // الالتزام بالميزانية: أقل من المخطط يرفع النتيجة

    private function calcPillarCapacity(float $totalBudget, float $totalActualCost, ?float $resourceEfficiency): float
    {
        if ($totalBudget <= 0) {
            return 0;
        }

        $spentRatio = $totalActualCost / $totalBudget;

        // أنفقنا أقل من المخطط = كفاءة = درجة أعلى
        $budgetScore = match (true) {
            $spentRatio <= 0.80 => 100,  // وفّرنا 20%+
            $spentRatio <= 0.90 => 85,
            $spentRatio <= 1.00 => 70,   // مطابق
            $spentRatio <= 1.10 => 50,
            default             => 20,
        };

        $efficiencyScore = min($resourceEfficiency ?? 50, 100);

        return round(($budgetScore * 0.60) + ($efficiencyScore * 0.40), 2);
    }

    // ─── الركيزة السادسة: الاستدامة المؤسسية ────────────────────────────────────
    // تغطية الأرباع (65%) + مدة التنفيذ (35%)

    private function calcPillarSustainability(int $quartersCount, ?int $executionDuration): float
    {
        // تغطية الأرباع: 4 أرباع = 100%
        $quartersScore = min(($quartersCount / 4) * 100, 100);

        // مدة التنفيذ: مرجع 365 يوم = 100%
        $durationScore = 0;
        if ($executionDuration !== null && $executionDuration > 0) {
            $durationScore = min(($executionDuration / 365) * 100, 100);
        }

        return round(($quartersScore * 0.65) + ($durationScore * 0.35), 2);
    }
}