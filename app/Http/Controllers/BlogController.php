<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogRequest;
use Orion\Http\Controllers\Controller as ApiController;
use Orion\Http\Requests\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;


class BlogController extends ApiController
{
    protected $model = \App\Models\Blog::class;
    protected $request = StoreBlogRequest::class;

    protected function authorizationRequired(): bool
    {
        return false;
    }

    /**
     * @bodyParam title string required Example: My Blog Post
     * @bodyParam slug string required Example: my-blog-post
     * @bodyParam short_description string required Example: A short desc
     * @bodyParam content string required Example: Full content here
     * @bodyParam author string required Example: John Doe
     * @bodyParam blog_category_id integer required Example: 1
     * @bodyParam image_path string Example: uploads/image.jpg
     * @bodyParam published_at string Example: 2026-01-01 00:00:00
     * @bodyParam is_published boolean Example: false
     */
    public function store(Request $request): JsonResource
{
    return parent::store($request);
}
}