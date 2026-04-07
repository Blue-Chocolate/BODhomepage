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
    /**
     * GET /api/releases
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $releases = Release::query()
            ->when($request->search, fn ($q, $s) =>
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('content_text', 'like', "%{$s}%")
            )
            ->orderBy('date', 'desc')
            ->paginate($request->integer('per_page', 15));

        return ReleaseResource::collection($releases);
    }

    /**
     * GET /api/releases/{release}
     */
    public function show(Release $release): ReleaseResource
    {
        return new ReleaseResource($release);
    }

    /**
     * POST /api/releases
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'row_number'          => 'nullable|integer',
            'edition_number'      => 'nullable|integer',
            'post_id'             => 'nullable|integer',
            'date'                => 'nullable|date',
            'modified'            => 'nullable|date',
            'status'              => 'nullable|string|max:50',
            'link'                => 'nullable|url|max:2048',
            'title'               => 'nullable|string|max:500',
            'excerpt'             => 'nullable|string',
            'content_text'        => 'nullable|string',
            'author_id'           => 'nullable|integer',
            'author_name'         => 'nullable|string|max:255',
            'image_url'           => 'nullable|url|max:2048',
            'image_drive_file_id' => 'nullable|string|max:255',
            'image_drive_link'    => 'nullable|url|max:2048',
            'image_file_name'     => 'nullable|string|max:500',
            'image_upload_status' => 'nullable|string|max:50',
            'categories'          => 'nullable|string|max:255',
            'tags'                => 'nullable|string|max:255',
            'slug'                => 'nullable|string|max:500',
            'reading_time'        => 'nullable|string|max:100',
        ]);

        $release = Release::create($validated);

        return (new ReleaseResource($release))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * PUT/PATCH /api/releases/{release}
     */
    public function update(Request $request, Release $release): ReleaseResource
    {
        $validated = $request->validate([
            'row_number'          => 'sometimes|nullable|integer',
            'edition_number'      => 'sometimes|nullable|integer',
            'post_id'             => 'sometimes|nullable|integer',
            'date'                => 'sometimes|nullable|date',
            'modified'            => 'sometimes|nullable|date',
            'status'              => 'sometimes|nullable|string|max:50',
            'link'                => 'sometimes|nullable|url|max:2048',
            'title'               => 'sometimes|nullable|string|max:500',
            'excerpt'             => 'sometimes|nullable|string',
            'content_text'        => 'sometimes|nullable|string',
            'author_id'           => 'sometimes|nullable|integer',
            'author_name'         => 'sometimes|nullable|string|max:255',
            'image_url'           => 'sometimes|nullable|url|max:2048',
            'image_drive_file_id' => 'sometimes|nullable|string|max:255',
            'image_drive_link'    => 'sometimes|nullable|url|max:2048',
            'image_file_name'     => 'sometimes|nullable|string|max:500',
            'image_upload_status' => 'sometimes|nullable|string|max:50',
            'categories'          => 'sometimes|nullable|string|max:255',
            'tags'                => 'sometimes|nullable|string|max:255',
            'slug'                => 'sometimes|nullable|string|max:500',
            'reading_time'        => 'sometimes|nullable|string|max:100',
        ]);

        $release->update($validated);

        return new ReleaseResource($release);
    }

    /**
     * DELETE /api/releases/{release}
     */
    public function destroy(Release $release): JsonResponse
    {
        $release->delete();

        return response()->json(['message' => 'Release deleted successfully.']);
    }
}