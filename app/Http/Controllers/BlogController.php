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

class BlogController extends Controller
{
    public function __construct(
        private readonly BlogRepository $repository
    ) {}

    public function index(): JsonResponse
    {
        $blogs = $this->repository->getAll();

        return response()->json($blogs);
    }

    public function show(int $id): JsonResponse
    {
        $blog = $this->repository->findById($id);

        return response()->json($blog);
    }

    public function store(StoreBlogRequest $request, StoreBlogAction $action): JsonResponse
    {
        $blog = $action->execute($request->validated());

        return response()->json($blog, 201);
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