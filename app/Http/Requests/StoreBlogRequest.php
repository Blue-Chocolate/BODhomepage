<?php

namespace App\Http\Requests;

use Orion\Http\Requests\Request;

class StoreBlogRequest extends Request
{
    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'max:255'],
            'slug'             => ['required', 'string', 'unique:blogs,slug'],
            'short_description'=> ['required', 'string'],
            'content'          => ['required', 'string'],
            'author'           => ['required', 'string'],
            'blog_category_id' => ['required', 'integer', 'exists:blog_categories,id'],
            'image_path'       => ['nullable', 'string'],
            'published_at'     => ['nullable', 'date'],
            'is_published'     => ['nullable', 'boolean'],
        ];
    }
}