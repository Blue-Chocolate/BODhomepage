<?php

namespace App\Actions\Blog;

use App\Models\Blog;
use App\Repositories\BlogRepository\BlogRepository;
use Illuminate\Support\Str;

class StoreBlogAction
{
    public function __construct(
        private readonly BlogRepository $repository
    ) {}

   public function execute(array $data): Blog|array
{
    if (!isset($data[0])) {
        return $this->createSingle($data);
    }

    return $this->createBulk($data);
}

private function createBulk(array $data): array
{
    $now = now();

    $prepared = array_map(function ($item) use ($now) {
        if (empty($item['slug'])) {
            $item['slug'] = Str::slug($item['title']) . '-' . uniqid();
        }

        return [
            'title'             => $item['title'],
            'slug'              => $item['slug'],
            'short_description' => $item['short_description'],
            'content'           => $item['content'],
            'author'            => $item['author'],
            'blog_category_id'  => $item['blog_category_id'],
            'image_url'        => $item['image_path'] ?? null,
            'published_at'      => $item['published_at'] ?? null,
            'is_published'      => $item['is_published'] ?? false,
            'created_at'        => $now,
            'updated_at'        => $now,
        ];
    }, $data);

    return $this->repository->createBulk($prepared);
}

private function createSingle(array $data): Blog
{
    if (empty($data['slug'])) {
        $data['slug'] = Str::slug($data['title']) . '-' . uniqid();
    }

    return $this->repository->create($data);
}
}