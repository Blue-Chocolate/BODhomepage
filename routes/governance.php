<?php

use App\Http\Controllers\Api\Governance\ImpactController;
use App\Http\Controllers\Api\Governance\ProgramController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Governance & Impact Routes
|--------------------------------------------------------------------------
| Prefix: /api/governance
|
| FLOW — وحدة الحوكمة:
|   1. POST   /programs                              → إنشاء برنامج
|   2. GET    /programs                              → قائمة البرامج
|   3. GET    /programs/{id}                         → تفاصيل البرنامج
|   4. PUT    /programs/{id}                         → تعديل البرنامج
|   5. POST   /programs/{id}/quarters               → إضافة/تحديث بيانات ربع
|         ↑ تتكرر لكل ربع (Q1-Q4)
|   6. GET    /programs/{id}/score                  → النتيجة الكاملة + الركائز الست
|
| FLOW — وحدة الأثر:
|   1. POST   /programs/{id}/impact                 → إنشاء/تحديث تقرير الأثر
|   2. GET    /programs/{id}/impact                 → عرض تقرير الأثر
|   3. POST   /programs/{id}/impact/beneficiaries   → تحديث سجلات المستفيدين
*/

Route::middleware(['auth:sanctum'])->group(function () {

    // ── Programs ──────────────────────────────────────────────────────────────────
    Route::get('programs',                [ProgramController::class, 'index']);
    Route::post('programs',               [ProgramController::class, 'store']);
    Route::get('programs/{program}',      [ProgramController::class, 'show']);
    Route::put('programs/{program}',      [ProgramController::class, 'update']);

    // ── Quarterly Data ────────────────────────────────────────────────────────────
    Route::post('programs/{program}/quarters', [ProgramController::class, 'saveQuarter']);

    // ── Governance Score ──────────────────────────────────────────────────────────
    Route::get('programs/{program}/score', [ProgramController::class, 'score']);

    // ── Impact ────────────────────────────────────────────────────────────────────
    Route::post('programs/{program}/impact',               [ImpactController::class, 'store']);
    Route::get('programs/{program}/impact',                [ImpactController::class, 'show']);
    Route::post('programs/{program}/impact/beneficiaries', [ImpactController::class, 'syncBeneficiaries']);
});