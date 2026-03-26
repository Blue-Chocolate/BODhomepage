<?php 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Orion\Facades\Orion;

use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogCateogryController;


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