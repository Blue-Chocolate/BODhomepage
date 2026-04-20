<?php

namespace App\Actions\Blog;

use App\Repositories\BlogRepository\BlogRepository;
use Illuminate\Support\Str;

class StoreBlogAction
{
    public function __construct(
        private readonly BlogRepository $repository
    ) {}

    public function execute(array $data, bool $isBulk = false): array|object
    {
        if ($isBulk) {
            return $this->repository->bulkCreate($data);
        }

        $data['slug'] ??= Str::slug($data['title']) . '-' . uniqid();
        return $this->repository->create($data);
    }
}