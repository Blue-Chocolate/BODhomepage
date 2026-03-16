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
        // Single blog
        if (!isset($data[0])) {
            return $this->createSingle($data);
        }

        // Multiple blogs
        return array_map(fn($item) => $this->createSingle($item), $data);
    }

    private function createSingle(array $data): Blog
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']) . '-' . uniqid();
        }

        return $this->repository->create($data);
    }
}