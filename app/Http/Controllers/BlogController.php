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
        $perPage = min((int) $request->query('per_page', 15), 100);
        $page    = (int) $request->query('page', 1);

        return response()->json($this->repository->getAll($perPage, $page));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->repository->findById($id));
    }

    public function showBySlug(string $slug): JsonResponse
    {
        return response()->json($this->repository->findBySlug($slug));
    }

public function store(StoreBlogRequest $request, StoreBlogAction $action): JsonResponse
{
    $isBulk = $request->isBulk();
    $data   = $isBulk
        ? $request->json()->all()
        : $request->validated();

    $result = $action->execute($data, $isBulk);

    return response()->json($result, 201);
}

    public function update(UpdateBlogRequest $request, Blog $blog, UpdateBlogAction $action): JsonResponse
    {
        return response()->json($action->execute($blog, $request->validated()));
    }

    public function destroy(Blog $blog, DeleteBlogAction $action): JsonResponse
    {
        $action->execute($blog);
        return response()->json(['message' => 'Blog deleted successfully']);
    }
}