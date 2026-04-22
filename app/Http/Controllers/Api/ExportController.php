<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Compliance\AssessmentSubmission;
use App\Services\Export\CompliancePdfService;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    public function __construct(
        private readonly CompliancePdfService $pdfService
    ) {}

    /**
     * GET /api/compliance/submissions/{submission}/export-pdf
     * تحميل تقرير الامتثال كـ PDF
     */
    public function complianceReport(AssessmentSubmission $submission): Response
    {
        // Guard: لازم يكون submitted على الأقل
        abort_if(
            $submission->status === 'draft',
            422,
            'لا يمكن تصدير تقرير في حالة مسودة — يجب تقديم التقييم أولاً'
        );

        $pdf      = $this->pdfService->generate($submission);
        $org      = $submission->organization->name;
        $year     = $submission->assessment->period_year;
        $filename = "governance-report-{$org}-{$year}.pdf";
        $filename = $this->sanitizeFilename($filename);

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Content-Length'      => strlen($pdf),
        ]);
    }

    private function sanitizeFilename(string $name): string
    {
        // إزالة الأحرف الغير مسموح بها في اسم الملف
        return preg_replace('/[^a-zA-Z0-9\-_\.]/', '-', $name);
    }
}