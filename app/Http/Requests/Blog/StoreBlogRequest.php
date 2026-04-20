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
                '*.image_url'         => ['nullable', 'string'],
                '*.published_at'      => ['nullable', 'date'],
                '*.is_published'      => ['nullable', 'boolean'],
                '*.short_description' => ['nullable', 'string'],
                '*.content'           => ['nullable', 'string'],
                '*.author'            => ['nullable', 'string', 'max:255'],
                '*.blog_category_id'  => ['nullable', 'integer'],
                '*.product_id'        => ['nullable', 'string'],
                '*.keyword'           => ['nullable', 'string'],
                '*.keyword_strength'  => ['nullable', 'numeric'],
                '*.search_intent'     => ['nullable', 'string'],
                '*.category_name'     => ['nullable', 'string'],
                '*.tags'              => ['nullable', 'string'],
                '*.meta_description'  => ['nullable', 'string'],
                '*.summary'           => ['nullable', 'string'],
                '*.content_html'      => ['nullable', 'string'],
                '*.word_count'        => ['nullable', 'integer'],
            ];
        }

        return [
            'title'             => ['required', 'string', 'max:255'],
            'slug'              => ['nullable', 'string', 'unique:blogs,slug'],
            'image_url'         => ['nullable', 'string'],
            'published_at'      => ['nullable', 'date'],
            'is_published'      => ['nullable', 'boolean'],
            'short_description' => ['nullable', 'string'],
            'content'           => ['nullable', 'string'],
            'author'            => ['nullable', 'string', 'max:255'],
            'blog_category_id'  => ['nullable', 'integer', 'exists:blog_categories,id'],
            'product_id'        => ['nullable', 'string'],
            'keyword'           => ['nullable', 'string'],
            'keyword_strength'  => ['nullable', 'numeric'],
            'search_intent'     => ['nullable', 'string'],
            'category_name'     => ['nullable', 'string'],
            'tags'              => ['nullable', 'string'],
            'meta_description'  => ['nullable', 'string'],
            'summary'           => ['nullable', 'string'],
            'content_html'      => ['nullable', 'string'],
            'word_count'        => ['nullable', 'integer'],
        ];
    }

    public function withValidator($validator): void
    {
        if (!$this->isBulk()) return;

        $validator->after(function ($validator) {
            $items = $this->getParsedBody();

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

                // validate category only if provided
                $catId = $item['blog_category_id'] ?? null;
                if ($catId && !\App\Models\BlogCategory::find($catId)) {
                    $validator->errors()->add(
                        "{$index}.blog_category_id",
                        "The blog category id {$catId} does not exist."
                    );
                }
            }
        });
    }

    // ✅ الفيكس الرئيسي — يقرأ الـ JSON صح
    public function isBulk(): bool
    {
        $body = $this->getParsedBody();
        return is_array($body) && array_is_list($body);
    }

    // helper مركزي لقراءة الـ body
    private function getParsedBody(): array
    {
        $body = $this->json()->all();

        // لو فاضي جرب all() العادي
        if (empty($body)) {
            $body = $this->all();
        }

        return $body;
    }
}