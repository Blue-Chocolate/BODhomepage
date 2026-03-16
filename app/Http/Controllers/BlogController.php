<?php

namespace App\Http\Controllers;

use App\Actions\Blog\DeleteBlogAction;
use App\Actions\Blog\StoreBlogAction;
use App\Actions\Blog\UpdateBlogAction;
use App\Http\Requests\Blog\StoreBlogRequest;
use App\Http\Requests\Blog\UpdateBlogRequest;
use App\Models\Blog;
use App\Repositories\BlogRepository\BlogRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct(
        private readonly BlogRepository $repository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $page    = (int) $request->query('page', 1);

        $blogs = $this->repository->getAll($perPage, $page);

        return response()->json($blogs);
    }

    public function show(int $id): JsonResponse
    {
        $blog = $this->repository->findById($id);

        return response()->json($blog);
    }

    public function store(StoreBlogRequest $request, StoreBlogAction $action): JsonResponse
    {
        $data = $request->isBulk()
            ? $request->json()->all()
            : $request->validated();

        $result = $action->execute($data);

        return response()->json($result, 201);
    }

    public function update(UpdateBlogRequest $request, Blog $blog, UpdateBlogAction $action): JsonResponse
    {
        $blog = $action->execute($blog, $request->validated());

        return response()->json($blog);
    }

    public function destroy(Blog $blog, DeleteBlogAction $action): JsonResponse
    {
        $action->execute($blog);

        return response()->json(['message' => 'Blog deleted successfully']);
    }
}
// ```

// ---

// Now you can control pagination via query params:
// ```
// GET /api/blogs                      → page 1, 15 per page (defaults)
// GET /api/blogs?per_page=30          → page 1, 30 per page
// GET /api/blogs?page=2               → page 2, 15 per page
// GET /api/blogs?per_page=50&page=3   → page 3, 50 per page
// GET /api/blogs?per_page=200         → capped at 100 per page