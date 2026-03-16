<?php

namespace App\Http\Controllers;

use Orion\Http\Controllers\Controller as ApiController;
use Orion\Http\Requests\Request;

class BlogController extends ApiController
{
    protected $model = \App\Models\Blog::class;

    protected function authorizationRequired(): bool
    {
        return false;
    }

    public function rules(): array
    {
        return [
            'store' => [
                'title'            => ['required', 'string', 'max:255'],
                'slug'             => ['required', 'string', 'unique:blogs,slug'],
                'short_description'=> ['required', 'string'],
                'content'          => ['required', 'string'],
                'author'           => ['required', 'string'],
                'blog_category_id' => ['required', 'integer', 'exists:blog_categories,id'],
                'image_path'       => ['nullable', 'string'],
                'published_at'     => ['nullable', 'date'],
                'is_published'     => ['nullable', 'boolean'],
            ],
            'update' => [
                'title'            => ['sometimes', 'string', 'max:255'],
                'slug'             => ['sometimes', 'string', 'unique:blogs,slug'],
                'short_description'=> ['sometimes', 'string'],
                'content'          => ['sometimes', 'string'],
                'author'           => ['sometimes', 'string'],
                'blog_category_id' => ['sometimes', 'integer', 'exists:blog_categories,id'],
                'image_path'       => ['nullable', 'string'],
                'published_at'     => ['nullable', 'date'],
                'is_published'     => ['nullable', 'boolean'],
            ],
        ];
    }
}