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
Orion::resource('case-studies', \App\Http\Controllers\CaseStudyController::class)->only(['index', 'show']);
Orion::resource('digital-solutions', \App\Http\Controllers\DigitalSolutionController::class)->only(['index', 'show']);
Orion::resource('digital-solution-types', \App\Http\Controllers\DigitalSolutionTypeController::class)->only(['index', 'show']);

use App\Http\Controllers\ContactUsController;

Route::apiResource('contact-us', ContactUsController::class);