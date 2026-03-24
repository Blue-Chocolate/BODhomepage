<?php

// app/Http/Controllers/Api/TestimonialsController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestimonialResource;
use App\Http\Resources\SuccessStoryResource;
use App\Models\CarouselSetting;
use App\Models\SuccessStory;
use App\Models\Testimonial;

class TestimonialsController extends Controller
{
    public function index()
    {
        return response()->json([
            'testimonials'    => TestimonialResource::collection(Testimonial::getActive()),
            'success_stories' => SuccessStoryResource::collection(SuccessStory::getActive()),
            'carousel'        => [
                'testimonials'    => CarouselSetting::forSection('testimonials'),
                'success_stories' => CarouselSetting::forSection('success_stories'),
            ],
        ]);
    }
}