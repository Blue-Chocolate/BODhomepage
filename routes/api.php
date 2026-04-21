<?php 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Orion\Facades\Orion;

use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogCateogryController;
use App\Http\Controllers\Api\AuthController\AuthController;
use App\Http\Middleware\CheckApprovedOrganization;
use App\Http\Controllers\Api\AnnualPlanController;
Route::apiResource('blogs', BlogController::class);

Orion::resource('blogs', BlogController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
Orion::resource('blog-categories', BlogCateogryController::class)->only(['index', 'show']);
Orion::resource('business-library', \App\Http\Controllers\BusinessLibraryController::class)->only(['index', 'show']);
Orion::resource('business-library-categories', \App\Http\Controllers\BusinessLibraryCategoryController::class)->only(['index', 'show']);
Orion::resource('digital-solutions', \App\Http\Controllers\DigitalSolutionController::class)->only(['index', 'show']);
Orion::resource('digital-solution-types', \App\Http\Controllers\DigitalSolutionTypeController::class)->only(['index', 'show']);

use App\Http\Controllers\ContactUsController;

Route::apiResource('contact-us', ContactUsController::class);

use App\Http\Controllers\PartnerController;

Route::get('partners', [PartnerController::class, 'index']);
Route::get('partners/{id}', [PartnerController::class, 'show']);

use App\Http\Controllers\Api\HeaderController;

Route::prefix('header')->group(function () {
    Route::get('/',        [HeaderController::class, 'index']);    // GET /api/header
    Route::get('/settings',[HeaderController::class, 'settings']); // GET /api/header/settings
    Route::get('/nav',     [HeaderController::class, 'nav']);      // GET /api/header/nav
});

// routes/api.php
use App\Http\Controllers\Api\WhoAreWeController;
use App\Http\Controllers\Api\ServicesController;
use App\Http\Controllers\Api\TestimonialsController;

// 4.3 من نحن
Route::get('/who-are-we', [WhoAreWeController::class, 'index']);

// 4.4 الخدمات
Route::get('/services',       [ServicesController::class, 'index']);
Route::get('/services/{service}', [ServicesController::class, 'show']);

// 4.6 الشهادات وقصص النجاح
Route::get('/testimonials', [TestimonialsController::class, 'index']);


// routes/api.php
use App\Http\Controllers\Api\NewsController;

Route::prefix('news')->group(function () {
    Route::get('/',            [NewsController::class, 'index']);      // GET /api/news
    Route::get('/categories',  [NewsController::class, 'categories']); // GET /api/news/categories
    Route::get('/{slug}',      [NewsController::class, 'show']);       // GET /api/news/{slug}
});

// routes/api.php
use App\Http\Controllers\Api\FooterController;

Route::get('/footer', [FooterController::class, 'index']);

// routes/api.php

use App\Http\Controllers\Api\CaseStudyController;

Route::prefix('case-studies')->group(function () {
    Route::get('/', [CaseStudyController::class, 'index']);
    Route::get('/{id}', [CaseStudyController::class, 'show']);

});

use App\Http\Controllers\Api\ReleaseController;

 Route::apiResource('releases', ReleaseController::class);

use App\Http\Controllers\Api\OrganizationController;

Route::prefix('organizations')->group(function () {
    Route::get('/',                         [OrganizationController::class, 'index']);
    Route::post('/',                        [OrganizationController::class, 'store']);
    Route::get('/{organization}',           [OrganizationController::class, 'show']);
    Route::put('/{organization}',           [OrganizationController::class, 'update']);
    Route::delete('/{organization}',        [OrganizationController::class, 'destroy']);
    Route::post('/{organization}/approve',  [OrganizationController::class, 'approve']);
    Route::post('/{organization}/reject',   [OrganizationController::class, 'reject']);
});
use App\Http\Controllers\Api\StrategicPlanController;

Route::prefix('strategic-plans')->group(function () {
    Route::get('/', [StrategicPlanController::class, 'index']);
    Route::get('{slug}', [StrategicPlanController::class, 'show']);
});
use App\Http\Controllers\Api\ProceduralEvidenceController;

Route::prefix('procedural-evidences')->group(function () {
    Route::get('/', [ProceduralEvidenceController::class, 'index']);
    Route::get('/{id}', [ProceduralEvidenceController::class, 'show']);
    Route::post('/', [ProceduralEvidenceController::class, 'store']);
    Route::put('/{id}', [ProceduralEvidenceController::class, 'update']);
    Route::delete('/{id}', [ProceduralEvidenceController::class, 'destroy']);
});

Route::apiResource('annual-plans', AnnualPlanController::class);


use App\Http\Controllers\Api\DigitalSolutionLinkController;

Route::prefix('digital-solution-links')->group(function () {
    Route::get('/', [DigitalSolutionLinkController::class, 'index']);
    Route::get('/{digitalSolutionLink}', [DigitalSolutionLinkController::class, 'show']);
});

use App\Http\Controllers\Api\HeroController;
Route::prefix('hero')->group(function () {
 
    // Full hero data (slides + statistics) for a site
    // GET /api/hero?site=waleda
    Route::get('/', [HeroController::class, 'index']);
 
    // Statistics bar only
    // GET /api/hero/statistics?site=waleda
    Route::get('/statistics', [HeroController::class, 'statistics']);
});
 
// ── Protected (Sanctum / admin token) ────────────────────────────────────────
Route::prefix('hero')->group(function () {
 
    // ── Slides ───────────────────────────────────────────────────────────────
    Route::get('/slides',                    [HeroController::class, 'slides']);         // paginated list
    Route::get('/slides/{id}',              [HeroController::class, 'showSlide']);       // single slide
    Route::post('/slides',                  [HeroController::class, 'storeSlide']);      // create
    Route::post('/slides/{id}',             [HeroController::class, 'updateSlide']);     // update (POST for multipart)
    Route::delete('/slides/{id}',           [HeroController::class, 'destroySlide']);    // soft delete
    Route::patch('/slides/reorder',         [HeroController::class, 'reorderSlides']);   // drag-and-drop order
    Route::patch('/slides/{id}/toggle',     [HeroController::class, 'toggleSlide']);     // toggle active
 
    // ── Statistics ────────────────────────────────────────────────────────────
    Route::post('/statistics',              [HeroController::class, 'storeStat']);
    Route::put('/statistics/{id}',          [HeroController::class, 'updateStat']);
    Route::delete('/statistics/{id}',       [HeroController::class, 'destroyStat']);
    Route::patch('/statistics/reorder',     [HeroController::class, 'reorderStats']);
});

use App\Http\Controllers\Api\SocialInitiativeController;
Route::prefix('social-initiatives')->group(function () {
    Route::get('/', [SocialInitiativeController::class, 'index']);
    Route::get('/{socialInitiative}', [SocialInitiativeController::class, 'show']);
});

use App\Http\Controllers\Api\Compliance\AssessmentController;
use App\Http\Controllers\Api\Compliance\SubmissionController;

Route::prefix('compliance')->middleware(['auth:sanctum'])->group(function () {
 
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
use App\Http\Controllers\Api\HomepageHeroSectionController;
Route::get('homepage/hero',    [HomepageHeroSectionController::class, 'show']);
