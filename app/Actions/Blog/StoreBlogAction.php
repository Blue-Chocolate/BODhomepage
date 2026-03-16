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

    public function execute(array $data): Blog
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        return $this->repository->create($data);
    }
}