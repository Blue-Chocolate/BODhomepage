<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomepageHeroSectionResource;
use App\Models\HomepageHeroSection;
use App\Traits\ApiErrorHandling;
use App\Traits\ApiLogging;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomepageHeroSectionController extends Controller
{
    use ApiErrorHandling, ApiLogging;

    public function show(): JsonResponse
    {
        try {
            $hero = HomepageHeroSection::firstOrCreate([]);

            return response()->json([
                'success' => true,
                'data'    => new HomepageHeroSectionResource($hero),
            ]);
        } catch (\Throwable $e) {
            return $this->handleApiError($e, 'Failed to fetch hero section');
        }
    }

    public function update(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title'                => 'sometimes|string|max:255',
                'description'          => 'sometimes|string',
                'background_image'     => 'sometimes|array',
                'background_image.*'   => 'string',
                'background_video_url' => 'sometimes|nullable|url',
                'text'                 => 'sometimes|string',
                'subtext'              => 'sometimes|string',
            ]);

            $hero = HomepageHeroSection::firstOrCreate([]);
            $hero->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Hero section updated successfully.',
                'data'    => new HomepageHeroSectionResource($hero),
            ]);
        } catch (\Throwable $e) {
            return $this->handleApiError($e, 'Failed to update hero section');
        }
    }
}