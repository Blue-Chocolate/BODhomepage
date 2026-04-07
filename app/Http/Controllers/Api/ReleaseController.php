<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReleaseResource;
use App\Models\Release;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReleaseController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $releases = Release::query()
            ->when($request->search, fn($q, $s) =>
                $q->where('title_guess', 'like', "%{$s}%")
                  ->orWhere('card_text', 'like', "%{$s}%")
                  ->orWhere('excerpt', 'like', "%{$s}%")
            )
            ->orderBy('edition_number')
            ->orderBy('row_number')
            ->paginate($request->integer('per_page', 15));

        return ReleaseResource::collection($releases);
    }

    public function show(Release $release): ReleaseResource
    {
        return new ReleaseResource($release);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'row_number' => 'nullable|integer',
            'edition_number' => 'nullable|integer',
            'file_url' => 'nullable|url|max:2048',
            'direct_download_url' => 'nullable|url|max:2048',
            'cover_image_url' => 'nullable|url|max:2048',
            'image_drive_link' => 'nullable|url|max:2048',
            'image_file_name' => 'nullable|string|max:255',
            'image_drive_file_id' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:255',
            'title_guess' => 'nullable|string|max:500',
            'excerpt' => 'nullable|string|max:1000',
            'card_text' => 'nullable|string',
            'image_upload_status' => 'nullable|string|max:50',
            'image_url' => 'nullable|url|max:2048',
        ]);

        $release = Release::create($validated);

        return (new ReleaseResource($release))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Release $release): ReleaseResource
    {
        $validated = $request->validate([
            'row_number' => 'sometimes|nullable|integer',
            'edition_number' => 'sometimes|nullable|integer',
            'file_url' => 'sometimes|nullable|url|max:2048',
            'direct_download_url' => 'sometimes|nullable|url|max:2048',
            'cover_image_url' => 'sometimes|nullable|url|max:2048',
            'image_drive_link' => 'sometimes|nullable|url|max:2048',
            'image_file_name' => 'sometimes|nullable|string|max:255',
            'image_drive_file_id' => 'sometimes|nullable|string|max:255',
            'button_text' => 'sometimes|nullable|string|max:255',
            'title_guess' => 'sometimes|nullable|string|max:500',
            'excerpt' => 'sometimes|nullable|string|max:1000',
            'card_text' => 'sometimes|nullable|string',
            'image_upload_status' => 'sometimes|nullable|string|max:50',
            'image_url' => 'sometimes|nullable|url|max:2048',
        ]);

        $release->update($validated);

        return new ReleaseResource($release);
    }

    public function destroy(Release $release): JsonResponse
    {
        $release->delete();

        return response()->json(['message' => 'Release deleted successfully.']);
    }
}