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
            'title'              => ['sometimes', 'string', 'max:255'],
            'slug'               => ['nullable', 'string', Rule::unique('blogs', 'slug')->ignore($blogId)],
            'image_url'          => ['nullable', 'string'],
            'published_at'       => ['nullable', 'date'],
            'is_published'       => ['nullable', 'boolean'],
            // old
            'short_description'  => ['nullable', 'string'],
            'content'            => ['nullable', 'string'],
            'author'             => ['nullable', 'string', 'max:255'],
            'blog_category_id'   => ['nullable', 'integer', 'exists:blog_categories,id'],
            // new
            'product_id'         => ['nullable', 'string'],
            'keyword'            => ['nullable', 'string'],
            'keyword_strength'   => ['nullable', 'numeric'],
            'search_intent'      => ['nullable', 'string'],
            'category_name'      => ['nullable', 'string'],
            'tags'               => ['nullable', 'string'],
            'meta_description'   => ['nullable', 'string'],
            'summary'            => ['nullable', 'string'],
            'content_html'       => ['nullable', 'string'],
            'word_count'         => ['nullable', 'integer'],
        ];
    }
}   