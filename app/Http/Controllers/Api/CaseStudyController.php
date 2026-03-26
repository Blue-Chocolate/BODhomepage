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

    
    

    
}