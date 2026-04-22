<?php

use App\Http\Controllers\Api\Compliance\AssessmentController;
use App\Http\Controllers\Api\Compliance\SubmissionController;
use App\Http\Controllers\Api\ExportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Compliance Assessment Routes
|--------------------------------------------------------------------------
| Prefix  : /api/compliance
| Middleware: auth:sanctum (adjust to your auth guard)
|
| FLOW:
|   1. GET    /assessments              → browse active assessments
|   2. GET    /assessments/{id}         → get full structure (axes + questions)
|   3. POST   /submissions/initiate     → start a submission (returns draft)
|   4. GET    /submissions/{id}         → view submission + progress
|   5. POST   /submissions/{id}/axes/{axisId}/answers  → save ONE axis (5 Qs)
|         ↑ repeat for each of the 10 axes
|   6. POST   /submissions/{id}/recommendations  → Section 5
|   7. POST   /submissions/{id}/submit           → lock & submit
|   8. POST   /submissions/{id}/review           → evaluator review notes
|   9. POST   /submissions/{id}/decide           → Section 6: management decision
*/

Route::middleware(['auth:sanctum'])->group(function () {

    // ── Assessments (templates) ───────────────────────────────────────────────
    Route::get('assessments',                                          [AssessmentController::class, 'index']);
    Route::get('assessments/{assessment}',                             [AssessmentController::class, 'show']);
    Route::get('assessments/{assessment}/axes/{axis}/questions',       [AssessmentController::class, 'axisQuestions']);

    // ── Submissions ───────────────────────────────────────────────────────────
    Route::get('submissions',                        [SubmissionController::class, 'index']);
    Route::post('submissions/initiate',              [SubmissionController::class, 'initiate']);
    Route::get('submissions/{submission}',           [SubmissionController::class, 'show']);

    // ── Per-axis answers (the main filling endpoint) ──────────────────────────
    Route::post(
        'submissions/{submission}/axes/{axis}/answers',
        [SubmissionController::class, 'saveAxisAnswers']
    );

    // ── Lifecycle ─────────────────────────────────────────────────────────────
    Route::get('submissions/{submission}/final-report',              [SubmissionController::class, 'finalReport']);
    Route::get('submissions/{submission}/export-pdf',                [ExportController::class, 'complianceReport']);
    Route::post('submissions/{submission}/submit',          [SubmissionController::class, 'submit']);
    Route::post('submissions/{submission}/review',          [SubmissionController::class, 'review']);
    Route::post('submissions/{submission}/decide',          [SubmissionController::class, 'decide']);
    Route::post('submissions/{submission}/recommendations', [SubmissionController::class, 'syncRecommendations']);
});