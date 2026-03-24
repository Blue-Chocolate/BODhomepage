<?php 

// app/Http/Controllers/Api/NewsController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsListResource;
use App\Http\Resources\NewsResource;
use App\Http\Resources\NewsCategoryResource;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * GET /api/news
     * قائمة الأخبار مع فلتر بالكاتيجوري والبحث والـ pagination
     */
    public function index(Request $request)
    {
        $news = News::with('categories')
            ->published()
            ->when($request->category, fn($q) =>
                $q->whereHas('categories', fn($q) => $q->where('slug', $request->category))
            )
            ->when($request->search, fn($q) =>
                $q->where('title', 'like', "%{$request->search}%")
            )
            ->paginate($request->get('per_page', 12));

        return NewsListResource::collection($news);
    }

    /**
     * GET /api/news/{slug}
     */
    public function show(string $slug)
    {
        $news = News::with(['categories', 'tags'])
            ->where('slug', $slug)
            ->where('status', 'publish')
            ->firstOrFail();

        return new NewsResource($news);
    }

    /**
     * GET /api/news/categories
     */
    public function categories()
    {
        return NewsCategoryResource::collection(
            NewsCategory::withCount('news')->get()
        );
    }
}