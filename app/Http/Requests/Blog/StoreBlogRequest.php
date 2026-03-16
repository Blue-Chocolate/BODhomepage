<?php

namespace App\Http\Requests\Blog;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'max:255'],
            'slug'              => ['nullable', 'string', 'unique:blogs,slug'],
            'short_description' => ['required', 'string'],
            'content'           => ['required', 'string'],
            'author'            => ['required', 'string', 'max:255'],
            'blog_category_id'  => ['required', 'integer', 'exists:blog_categories,id'],
            'image_path'        => ['nullable', 'string'],
            'published_at'      => ['nullable', 'date'],
            'is_published'      => ['nullable', 'boolean'],
        ];
    }
}