<?php

namespace App\Http\Requests\Blog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $blogId = $this->route('blog');

        return [
            'title'             => ['sometimes', 'string', 'max:255'],
            'slug'              => ['nullable', 'string', Rule::unique('blogs', 'slug')->ignore($blogId)],
            'short_description' => ['sometimes', 'string'],
            'content'           => ['sometimes', 'string'],
            'author'            => ['sometimes', 'string', 'max:255'],
            'blog_category_id'  => ['sometimes', 'integer', 'exists:blog_categories,id'],
            'image_path'        => ['nullable', 'string'],
            'published_at'      => ['nullable', 'date'],
            'is_published'      => ['nullable', 'boolean'],
        ];
    }
}