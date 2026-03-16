<?php

namespace App\Http\Requests\Blog;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\BlogCategory;

class StoreBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->isBulk()) {
            return [
                '*.title'             => ['required', 'string', 'max:255'],
                '*.slug'              => ['nullable', 'string'],
                '*.short_description' => ['required', 'string'],
                '*.content'           => ['required', 'string'],
                '*.author'            => ['required', 'string', 'max:255'],
                '*.blog_category_id'  => ['required', 'integer'],
                '*.image_path'        => ['nullable', 'string'],
                '*.published_at'      => ['nullable', 'date'],
                '*.is_published'      => ['nullable', 'boolean'],
            ];
        }

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

    public function withValidator($validator): void
    {
        if (!$this->isBulk()) return;

        $validator->after(function ($validator) {
            $items = $this->json()->all();

            // Collect all unique category IDs in one query instead of 281
            $categoryIds = collect($items)->pluck('blog_category_id')->unique()->filter();
            $validIds    = BlogCategory::whereIn('id', $categoryIds)->pluck('id');

            foreach ($items as $index => $item) {
                if (!$validIds->contains($item['blog_category_id'] ?? null)) {
                    $validator->errors()->add(
                        "{$index}.blog_category_id",
                        "The blog category id {$item['blog_category_id']} does not exist."
                    );
                }
            }

            // Check slug uniqueness in one query
            $slugs      = collect($items)->pluck('slug')->unique()->filter();
            $takenSlugs = \App\Models\Blog::whereIn('slug', $slugs)->pluck('slug');

            foreach ($items as $index => $item) {
                if (!empty($item['slug']) && $takenSlugs->contains($item['slug'])) {
                    $validator->errors()->add(
                        "{$index}.slug",
                        "The slug '{$item['slug']}' is already taken."
                    );
                }
            }
        });
    }

    public function isBulk(): bool
    {
        return is_array($this->json()->all()) && isset($this->json()->all()[0]);
    }
}