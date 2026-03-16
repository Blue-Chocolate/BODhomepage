<?php

namespace App\Http\Requests;

use Orion\Http\Requests\Request; // ← must be Orion's, not Illuminate's

class StoreBlogRequest extends Request
{
    public function rules(): array
    {
        return [
            'store' => [  // ← Orion needs the operation key
                'title'             => ['required', 'string', 'max:255'],
                'slug'              => ['required', 'string', 'unique:blogs,slug'],
                'short_description' => ['required', 'string'],
                'content'           => ['required', 'string'],
                'author'            => ['required', 'string'],
                'blog_category_id'  => ['required', 'integer', 'exists:blog_categories,id'],
                'image_path'        => ['nullable', 'string'],
                'published_at'      => ['nullable', 'date'],
                'is_published'      => ['nullable', 'boolean'],
            ],
            'update' => [
                'title'             => ['sometimes', 'string', 'max:255'],
                'slug'              => ['sometimes', 'string', 'unique:blogs,slug'],
                'short_description' => ['sometimes', 'string'],
                'content'           => ['sometimes', 'string'],
                'author'            => ['sometimes', 'string'],
                'blog_category_id'  => ['sometimes', 'integer', 'exists:blog_categories,id'],
                'image_path'        => ['nullable', 'string'],
                'published_at'      => ['nullable', 'date'],
                'is_published'      => ['nullable', 'boolean'],
            ],
        ];
    }
}