<?php

namespace App\Actions\Blog;

use App\Models\Blog;
use App\Repositories\BlogRepository\BlogRepository;

class DeleteBlogAction
{
    public function __construct(
        private readonly BlogRepository $repository
    ) {}

    public function execute(Blog $blog): void
    {
        $this->repository->delete($blog);
    }
}