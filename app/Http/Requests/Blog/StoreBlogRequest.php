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
                '*.title'              => ['required', 'string', 'max:255'],
                '*.slug'               => ['nullable', 'string'],
                '*.image_url'          => ['nullable', 'string'],
                '*.published_at'       => ['nullable', 'date'],
                '*.is_published'       => ['nullable', 'boolean'],
                // old
                '*.short_description'  => ['nullable', 'string'],
                '*.content'            => ['nullable', 'string'],
                '*.author'             => ['nullable', 'string', 'max:255'],
                '*.blog_category_id'   => ['nullable', 'integer'],
                // new
                '*.product_id'         => ['nullable', 'string'],
                '*.keyword'            => ['nullable', 'string'],
                '*.keyword_strength'   => ['nullable', 'numeric'],
                '*.search_intent'      => ['nullable', 'string'],
                '*.category_name'      => ['nullable', 'string'],
                '*.tags'               => ['nullable', 'string'],
                '*.meta_description'   => ['nullable', 'string'],
                '*.summary'            => ['nullable', 'string'],
                '*.content_html'       => ['nullable', 'string'],
                '*.word_count'         => ['nullable', 'integer'],
            ];
        }

        return [
            'title'              => ['required', 'string', 'max:255'],
            'slug'               => ['nullable', 'string', 'unique:blogs,slug'],
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

    public function withValidator($validator): void
    {
        if (!$this->isBulk()) return;

        $validator->after(function ($validator) {
            $items = $this->json()->all();

            // validate category IDs in one query
            $categoryIds = collect($items)->pluck('blog_category_id')->unique()->filter();
            $validIds    = BlogCategory::whereIn('id', $categoryIds)->pluck('id');

            foreach ($items as $index => $item) {
                $catId = $item['blog_category_id'] ?? null;
                if ($catId && !$validIds->contains($catId)) {
                    $validator->errors()->add(
                        "{$index}.blog_category_id",
                        "The blog category id {$catId} does not exist."
                    );
                }
            }

            // validate slug uniqueness in one query
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