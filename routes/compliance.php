<?php 

use App\Http\Controllers\Api\Compliance\AssessmentController;
use App\Http\Controllers\Api\Compliance\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
 
    // ── Assessments (templates) ───────────────────────────────────────────────
    Route::get('assessments',                                          [AssessmentController::class, 'index']);
    Route::get('assessments/{assessment}',                             [AssessmentController::class, 'show']);
    Route::get('axes/{axis}/questions',                                [AssessmentController::class, 'axisQuestions']); 
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
    Route::post('submissions/{submission}/submit',          [SubmissionController::class, 'submit']);
    Route::post('submissions/{submission}/review',          [SubmissionController::class, 'review']);
     Route::post('submissions/{submission}/decide',          [SubmissionController::class, 'decide']);
    Route::post('submissions/{submission}/recommendations', [SubmissionController::class, 'syncRecommendations']);
});