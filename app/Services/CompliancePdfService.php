<?php

namespace App\Services\Export;

use App\Models\Compliance\AssessmentSubmission;
use App\Services\Compliance\ComplianceSubmissionService;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

class CompliancePdfService
{
    public function __construct(
        private readonly ComplianceSubmissionService $submissionService
    ) {}

    public function generate(AssessmentSubmission $submission): string
    {
        $data = $this->submissionService->buildFinalReport($submission);

        $mpdf = $this->createMpdf();

        // Set RTL document
        $mpdf->SetDirectionality('rtl');

        // Header & Footer
        $mpdf->SetHTMLHeader($this->buildHeader($data));
        $mpdf->SetHTMLFooter($this->buildFooter());

        // Cover page
        $mpdf->AddPage();
        $mpdf->WriteHTML($this->buildCoverPage($data));

        // Executive summary
        $mpdf->AddPage();
        $mpdf->WriteHTML($this->buildSummaryPage($data));

        // Axis breakdown pages
        $mpdf->AddPage();
        $mpdf->WriteHTML($this->buildBreakdownPage($data));

        // Recommendations + management decision
        $mpdf->AddPage();
        $mpdf->WriteHTML($this->buildRecommendationsPage($data));

        // Closing page
        $mpdf->AddPage();
        $mpdf->WriteHTML($this->buildClosingPage($data));

        return $mpdf->Output('', 'S'); // Return as string
    }

    // ─── mPDF Instance ────────────────────────────────────────────────────────────

    private function createMpdf(): Mpdf
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs      = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData          = $defaultFontConfig['fontdata'];

        return new Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'orientation'   => 'P',
            'margin_top'    => 20,
            'margin_bottom' => 20,
            'margin_left'   => 10,
            'margin_right'  => 10,
            'fontDir'       => array_merge($fontDirs, [
                storage_path('fonts'), // ضع خط Almarai هنا
            ]),
            'fontdata' => array_merge($fontData, [
                'almarai' => [
                    'R'  => 'Almarai-Regular.ttf',
                    'B'  => 'Almarai-Bold.ttf',
                    'I'  => 'Almarai-Regular.ttf',
                    'BI' => 'Almarai-Bold.ttf',
                ],
            ]),
            'default_font'       => 'almarai',
            'default_font_size'  => 11,
            'directionality'     => 'rtl',
            'autoLangToFont'     => true,
            'autoScriptToLang'   => true,
        ]);
    }

    // ─── CSS ──────────────────────────────────────────────────────────────────────

    private function baseStyles(): string
    {
        return <<<CSS
        <style>
            * { font-family: almarai, sans-serif; direction: rtl; }
            body { background: #F9F5F0; color: #242423; font-size: 11pt; }

            .cover { text-align: center; padding: 60px 20px; }
            .cover h1 { font-size: 28pt; color: #FF6900; margin-bottom: 10px; }
            .cover h2 { font-size: 18pt; color: #242423; margin-bottom: 30px; }
            .cover .meta { border: 1px solid #ddd; padding: 20px; background: #fff; border-radius: 8px; }
            .cover .meta table { width: 100%; }
            .cover .meta td { padding: 8px 12px; border-bottom: 1px solid #eee; }
            .cover .meta td:first-child { color: #666; width: 40%; }
            .cover .meta td:last-child { font-weight: bold; }

            .page-title { font-size: 18pt; color: #FF6900; border-bottom: 2px solid #FF6900;
                          padding-bottom: 8px; margin-bottom: 20px; }

            .score-badge { display: inline-block; padding: 6px 16px; border-radius: 20px;
                           font-weight: bold; font-size: 14pt; color: #fff; }

            .kpi-grid { width: 100%; margin-bottom: 20px; }
            .kpi-grid td { width: 25%; padding: 5px; }
            .kpi-card { background: #fff; border: 1px solid #eee; border-radius: 8px;
                        padding: 12px; text-align: center; }
            .kpi-card .kpi-value { font-size: 18pt; font-weight: bold; color: #FF6900; }
            .kpi-card .kpi-label { font-size: 9pt; color: #666; margin-top: 4px; }

            .axis-row { background: #fff; border: 1px solid #eee; border-radius: 6px;
                        margin-bottom: 8px; padding: 10px 14px; }
            .axis-row .axis-title { font-weight: bold; font-size: 11pt; }
            .axis-row .axis-score { font-size: 13pt; font-weight: bold; }
            .axis-row .axis-platform { font-size: 9pt; color: #888; margin-top: 2px; }

            .progress-bar-bg { background: #eee; border-radius: 4px; height: 8px; margin-top: 6px; }
            .progress-bar-fill { height: 8px; border-radius: 4px; }

            .question-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 9pt; }
            .question-table th { background: #FF6900; color: #fff; padding: 6px 10px; text-align: right; }
            .question-table td { padding: 5px 10px; border-bottom: 1px solid #eee; }
            .question-table tr:nth-child(even) { background: #fafafa; }

            .rec-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            .rec-table th { background: #242423; color: #fff; padding: 8px 12px; text-align: right; }
            .rec-table td { padding: 7px 12px; border-bottom: 1px solid #eee; }
            .badge-high   { background: #EF4444; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 9pt; }
            .badge-medium { background: #F59E0B; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 9pt; }
            .badge-low    { background: #10B981; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 9pt; }

            .closing { text-align: center; padding: 60px 20px; }
            .closing .tagline { font-size: 16pt; color: #FF6900; margin-bottom: 10px; }
            .closing .subline { font-size: 12pt; color: #666; }
            .closing .sign-box { border: 1px solid #ddd; border-radius: 8px; padding: 20px;
                                  margin-top: 40px; background: #fff; }
        </style>
        CSS;
    }

    // ─── Header / Footer ─────────────────────────────────────────────────────────

    private function buildHeader(array $data): string
    {
        return <<<HTML
        <table width="100%" style="font-family: almarai; direction: rtl; border-bottom: 1px solid #FF6900; padding-bottom: 5px;">
            <tr>
                <td style="font-size: 9pt; color: #666;">{$data['organization']['name']}</td>
                <td style="text-align: center; font-size: 9pt; color: #FF6900; font-weight: bold;">تقرير الامتثال السنوي</td>
                <td style="text-align: left; font-size: 9pt; color: #666;">{$data['assessment']['period_year']}</td>
            </tr>
        </table>
        HTML;
    }

    private function buildFooter(): string
    {
        return <<<HTML
        <table width="100%" style="font-family: almarai; direction: rtl; border-top: 1px solid #eee; padding-top: 5px;">
            <tr>
                <td style="font-size: 8pt; color: #888;">منصة أداء — ولادة حلم للاستشارات والأبحاث</td>
                <td style="text-align: center; font-size: 8pt; color: #888;">{PAGENO} / {nbpg}</td>
                <td style="text-align: left; font-size: 8pt; color: #888;">© 2026 جميع الحقوق محفوظة</td>
            </tr>
        </table>
        HTML;
    }

    // ─── Page 1: Cover ────────────────────────────────────────────────────────────

    private function buildCoverPage(array $data): string
    {
        $styles = $this->baseStyles();
        $org    = $data['organization'];
        $assess = $data['assessment'];
        $sub    = $data['submission'];
        $level  = $sub['compliance_level'] ?? '—';
        $score  = $sub['overall_score'] ? number_format($sub['overall_score'], 2) . ' / 5' : '—';
        $color  = $sub['compliance_color'] ?? '#666';
        $date   = $sub['submitted_at'] ? date('Y-m-d', strtotime($sub['submitted_at'])) : date('Y-m-d');

        return <<<HTML
        {$styles}
        <div class="cover">
            <h1>منصة أداء</h1>
            <h2>تقرير الامتثال السنوي</h2>

            <div class="meta">
                <table>
                    <tr><td>اسم الجهة</td><td>{$org['name']}</td></tr>
                    <tr><td>نوع الجهة</td><td>{$org['type']}</td></tr>
                    <tr><td>نموذج التقييم</td><td>{$assess['title']}</td></tr>
                    <tr><td>فترة التقييم</td><td>{$assess['period_year']}</td></tr>
                    <tr><td>تاريخ التقديم</td><td>{$date}</td></tr>
                    <tr>
                        <td>النتيجة الإجمالية</td>
                        <td>
                            <span class="score-badge" style="background:{$color};">{$score}</span>
                            &nbsp; {$level}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        HTML;
    }

    // ─── Page 2: Executive Summary ────────────────────────────────────────────────

    private function buildSummaryPage(array $data): string
    {
        $styles     = $this->baseStyles();
        $sub        = $data['submission'];
        $completion = $data['completion'];
        $score      = $sub['overall_score'] ? number_format($sub['overall_score'], 2) . ' / 5' : '—';
        $level      = $sub['compliance_level'] ?? '—';
        $color      = $sub['compliance_color'] ?? '#666';
        $message    = $sub['compliance_message'] ?? '';
        $percentage = $completion['percentage'];
        $answered   = $completion['answered_questions'];
        $total      = $completion['total_questions'];

        return <<<HTML
        {$styles}
        <div class="page-title">الملخص التنفيذي</div>

        <table class="kpi-grid">
            <tr>
                <td><div class="kpi-card">
                    <div class="kpi-value" style="color:{$color};">{$score}</div>
                    <div class="kpi-label">النتيجة الإجمالية</div>
                </div></td>
                <td><div class="kpi-card">
                    <div class="kpi-value">{$level}</div>
                    <div class="kpi-label">مستوى الامتثال</div>
                </div></td>
                <td><div class="kpi-card">
                    <div class="kpi-value">{$percentage}%</div>
                    <div class="kpi-label">نسبة الإكمال</div>
                </div></td>
                <td><div class="kpi-card">
                    <div class="kpi-value">{$answered} / {$total}</div>
                    <div class="kpi-label">الأسئلة المجابة</div>
                </div></td>
            </tr>
        </table>

        <div style="background:#fff; border-right: 4px solid {$color}; padding: 12px 16px;
                    border-radius: 6px; margin-bottom: 20px; font-size: 11pt;">
            {$message}
        </div>

        <div class="page-title" style="font-size: 14pt;">نتائج المحاور</div>
        {$this->buildAxesSummaryTable($data['axis_breakdown'])}
        HTML;
    }

    private function buildAxesSummaryTable(array $axes): string
    {
        $rows = '';
        foreach ($axes as $axis) {
            $score   = $axis['axis_score'] !== null ? number_format($axis['axis_score'], 2) . ' / 5' : '—';
            $color   = $axis['axis_color'] ?? '#666';
            $width   = $axis['axis_score'] !== null ? ($axis['axis_score'] / 5) * 100 : 0;

            $rows .= <<<HTML
            <div class="axis-row">
                <table width="100%"><tr>
                    <td width="60%">
                        <div class="axis-title">{$axis['title']}</div>
                        <div class="axis-platform">{$axis['recommendation_platform']}</div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width:{$width}%; background:{$color};"></div>
                        </div>
                    </td>
                    <td width="20%" style="text-align:center;">
                        <span class="axis-score" style="color:{$color};">{$score}</span>
                    </td>
                    <td width="20%" style="text-align:center; font-size:9pt; color:#888;">
                        {$axis['axis_level']}
                    </td>
                </tr></table>
            </div>
            HTML;
        }

        return $rows;
    }

    // ─── Page 3: Axis Breakdown ───────────────────────────────────────────────────

    private function buildBreakdownPage(array $data): string
    {
        $styles  = $this->baseStyles();
        $content = "<div class=\"page-title\">التحليل التفصيلي — المحاور والأسئلة</div>";

        foreach ($data['axis_breakdown'] as $axis) {
            $score  = $axis['axis_score'] !== null ? number_format($axis['axis_score'], 2) . ' / 5' : '—';
            $color  = $axis['axis_color'] ?? '#666';

            $content .= <<<HTML
            <div style="margin-bottom: 16px;">
                <table width="100%" style="background:#FF6900; border-radius:6px; padding:8px;">
                    <tr>
                        <td style="color:#fff; font-weight:bold; font-size:11pt;">{$axis['title']}</td>
                        <td style="text-align:left; color:#fff; font-size:12pt; font-weight:bold;">{$score}</td>
                    </tr>
                </table>
                <table class="question-table">
                    <thead>
                        <tr>
                            <th width="60%">السؤال</th>
                            <th width="15%">الدرجة</th>
                            <th width="25%">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
            HTML;

            foreach ($axis['questions'] as $q) {
                $score     = $q['score'] !== null ? $q['score'] . ' / 5' : '—';
                $notes     = $q['notes'] ?? '—';
                $content  .= "<tr><td>{$q['title']}</td><td style=\"text-align:center;\">{$score}</td><td>{$notes}</td></tr>";
            }

            $content .= '</tbody></table></div>';
        }

        return $styles . $content;
    }

    // ─── Page 4: Recommendations ─────────────────────────────────────────────────

    private function buildRecommendationsPage(array $data): string
    {
        $styles = $this->baseStyles();
        $sub    = $data['submission'];

        $priorityLabels = ['high' => 'عالية', 'medium' => 'متوسطة', 'low' => 'منخفضة'];
        $priorityBadges = ['high' => 'badge-high', 'medium' => 'badge-medium', 'low' => 'badge-low'];

        $recRows = '';
        foreach ($data['recommendations'] as $rec) {
            $priority    = $rec['priority'];
            $badgeClass  = $priorityBadges[$priority] ?? 'badge-low';
            $label       = $priorityLabels[$priority] ?? $priority;
            $responsible = $rec['responsible_party'] ?? '—';
            $recRows    .= <<<HTML
            <tr>
                <td><span class="{$badgeClass}">{$label}</span></td>
                <td>{$rec['recommendation']}</td>
                <td>{$responsible}</td>
            </tr>
            HTML;
        }

        $actionLabels = [
            'approved'           => 'اعتماد الوضع الحالي',
            'approved_with_plan' => 'اعتماد مع خطة تحسين',
            'urgent_treatment'   => 'تحويل للمعالجة العاجلة',
            'reassess'           => 'إعادة تقييم',
        ];
        $action   = $actionLabels[$sub['management_action'] ?? ''] ?? '—';
        $decision = $sub['management_decision'] ?? '—';
        $reassess = $sub['reassess_months'] ? "خلال {$sub['reassess_months']} أشهر" : '';

        return <<<HTML
        {$styles}
        <div class="page-title">التوصيات — القسم الخامس</div>

        <table class="rec-table">
            <thead>
                <tr>
                    <th width="15%">الأولوية</th>
                    <th width="60%">التوصية</th>
                    <th width="25%">الجهة المسؤولة</th>
                </tr>
            </thead>
            <tbody>{$recRows}</tbody>
        </table>

        <div class="page-title" style="margin-top: 30px;">قرار الإدارة — القسم السادس</div>
        <div style="background:#fff; border:1px solid #eee; border-radius:8px; padding:16px;">
            <table width="100%">
                <tr>
                    <td width="30%" style="color:#666;">القرار</td>
                    <td><strong>{$action}</strong> {$reassess}</td>
                </tr>
                <tr>
                    <td style="color:#666; padding-top:10px;">نص القرار</td>
                    <td style="padding-top:10px;">{$decision}</td>
                </tr>
            </table>
        </div>
        HTML;
    }

    // ─── Page 5: Closing ─────────────────────────────────────────────────────────

    private function buildClosingPage(array $data): string
    {
        $styles  = $this->baseStyles();
        $orgName = $data['organization']['name'];
        $date    = date('Y-m-d');

        return <<<HTML
        {$styles}
        <div class="closing">
            <div class="tagline">الامتثال ليس تفتيشًا</div>
            <div class="subline">الامتثال = استعداد + استدامة + ثقة</div>

            <div class="sign-box">
                <table width="100%">
                    <tr>
                        <td style="text-align:center; width:50%;">
                            <div style="border-bottom: 1px solid #333; margin-bottom:8px; height:40px;"></div>
                            <div style="font-size:10pt; color:#666;">توقيع ممثل الجهة</div>
                            <div style="font-size:10pt; font-weight:bold;">{$orgName}</div>
                        </td>
                        <td style="text-align:center; width:50%;">
                            <div style="border-bottom: 1px solid #333; margin-bottom:8px; height:40px;"></div>
                            <div style="font-size:10pt; color:#666;">تاريخ الإصدار</div>
                            <div style="font-size:10pt; font-weight:bold;">{$date}</div>
                        </td>
                    </tr>
                </table>
            </div>

            <div style="margin-top:40px; font-size:9pt; color:#aaa;">
                منصة أداء — ولادة حلم للاستشارات والأبحاث © 2026
            </div>
        </div>
        HTML;
    }
}