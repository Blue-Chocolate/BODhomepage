<?php

namespace App\Repositories\BlogRepository;

use App\Models\Blog;
use Illuminate\Pagination\LengthAwarePaginator;

class BlogRepository
{
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return Blog::with('category')
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): ?Blog
    {
        return Blog::with('category')->findOrFail($id);
    }

    public function findBySlug(string $slug): ?Blog
    {
        return Blog::with('category')->where('slug', $slug)->firstOrFail();
    }

    public function create(array $data): Blog
    {
        return Blog::create($data);
    }

    public function update(Blog $blog, array $data): Blog
    {
        $blog->update($data);
        return $blog->fresh('category');
    }

    public function delete(Blog $blog): void
    {
        $blog->delete();
    }
}