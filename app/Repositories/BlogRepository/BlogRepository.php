<?php

namespace App\Repositories\BlogRepository;

use App\Models\Blog;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class BlogRepository
{
    public function getAll(int $perPage, int $page): array
    {
        $blogs = Blog::with('category')
            ->where('is_published', true)
            ->latest('published_at')
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'data'         => collect($blogs->items())->map(fn($b) => $this->format($b)),
            'total'        => $blogs->total(),
            'current_page' => $blogs->currentPage(),
            'last_page'    => $blogs->lastPage(),
        ];
    }

    public function findById(int $id): array
    {
        $blog = Blog::with('category')->findOrFail($id);
        return $this->format($blog);
    }

    public function findBySlug(string $slug): array
    {
        $blog = Blog::with('category')->where('slug', $slug)->firstOrFail();
        return $this->format($blog);
    }

    public function create(array $data): Blog
    {
        return Blog::create($data);
    }

    public function bulkCreate(array $items): array
    {
        $created = [];
        foreach ($items as $item) {
            $item['slug'] ??= \Str::slug($item['title']) . '-' . uniqid();
            $created[]     = Blog::create($item);
        }
        return $created;
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

    private function format(Blog $blog): array
    {
        return [
            'id'                 => $blog->id,
            'title'              => $blog->title,
            'slug'               => $blog->slug,
            'image_url'          => $blog->image_url,
            'published_at'       => $blog->published_at,
            'is_published'       => $blog->is_published,
            // old
            'short_description'  => $blog->short_description,
            'content'            => $blog->content,
            'author'             => $blog->author,
            'category'           => $blog->category?->name,
            // new
            'product_id'         => $blog->product_id,
            'keyword'            => $blog->keyword,
            'keyword_strength'   => $blog->keyword_strength,
            'search_intent'      => $blog->search_intent,
            'category_name'      => $blog->category_name,
            'tags'               => $blog->tags_array,
            'meta_description'   => $blog->meta_description,
            'summary'            => $blog->summary,
            'content_html'       => $blog->content_html,
            'word_count'         => $blog->word_count,
        ];
    }
}