<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CaseStudyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = CaseStudy::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('excerpt', 'like', "%{$request->search}%")
                  ->orWhere('content_text', 'like', "%{$request->search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $caseStudies = $query->latest('published_at')->paginate($perPage);

        return response()->json($caseStudies);
    }

    public function show(string $id): JsonResponse
    {
        $caseStudy = CaseStudy::where('id', $id)
            ->orWhere('slug', $id)
            ->firstOrFail();

        return response()->json($caseStudy);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'slug'                => 'nullable|string|unique:case_studies,slug',
            'excerpt'             => 'nullable|string',
            'content_text'        => 'nullable|string',
            'status'              => 'in:publish,draft,private',
            'link'                => 'nullable|url',
            'image_url'           => 'nullable|url',
            'image_drive_file_id' => 'nullable|string',
            'image_drive_link'    => 'nullable|url',
            'image_file_name'     => 'nullable|string',
            'image_upload_status' => 'nullable|string',
            'author_id'           => 'nullable|integer',
            'author_name'         => 'nullable|string|max:255',
            'category_id'         => 'nullable|integer',
            'tags'                => 'nullable|string',
            'reading_time'        => 'nullable|string',
            'published_at'        => 'nullable|date',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);

        $caseStudy = CaseStudy::create($validated);

        return response()->json($caseStudy, 201);
    }

    public function update(Request $request, CaseStudy $caseStudy): JsonResponse
    {
        $validated = $request->validate([
            'title'               => 'sometimes|required|string|max:255',
            'slug'                => "nullable|string|unique:case_studies,slug,{$caseStudy->id}",
            'excerpt'             => 'nullable|string',
            'content_text'        => 'nullable|string',
            'status'              => 'in:publish,draft,private',
            'link'                => 'nullable|url',
            'image_url'           => 'nullable|url',
            'image_drive_file_id' => 'nullable|string',
            'image_drive_link'    => 'nullable|url',
            'image_file_name'     => 'nullable|string',
            'image_upload_status' => 'nullable|string',
            'author_id'           => 'nullable|integer',
            'author_name'         => 'nullable|string|max:255',
            'category_id'         => 'nullable|integer',
            'tags'                => 'nullable|string',
            'reading_time'        => 'nullable|string',
            'published_at'        => 'nullable|date',
        ]);

        $caseStudy->update($validated);

        return response()->json($caseStudy);
    }

    public function destroy(CaseStudy $caseStudy): JsonResponse
    {
        $caseStudy->delete();

        return response()->json(['message' => 'تم الحذف بنجاح'], 200);
    }
}