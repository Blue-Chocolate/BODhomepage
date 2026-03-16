<?php

namespace App\Actions\Blog;

use App\Models\Blog;
use App\Repositories\BlogRepository\BlogRepository;
use Illuminate\Support\Str;

class UpdateBlogAction
{
    public function __construct(
        private readonly BlogRepository $repository
    ) {}

    public function execute(Blog $blog, array $data): Blog
    {
        if (isset($data['title']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        return $this->repository->update($blog, $data);
    }
}