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

    protected function prepareForValidation(): void
    {
        $items = $this->json()->all();

        if (empty($items)) return;

        $isBulk = is_array($items) && array_is_list($items);

        if ($isBulk) {
            $mapped = array_map(fn($item) => $this->mapFields($item), $items);
            $this->replace($mapped);
        } else {
            $this->replace($this->mapFields($items));
        }
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
            $items = $this->all();

            // slug uniqueness in one query
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

    public function isBulk(): bool
    {
        $body = $this->json()->all();
        return is_array($body) && array_is_list($body);
    }

    private function mapFields(array $item): array
    {
        return [
            'title'             => $item['عنوان المقال'] ?? $item['title'] ?? null,
            'slug'              => $item['slug'] ?? null,
            'image_url'         => $item['صورة المقال'] ?? $item['image_url'] ?? null,
            'published_at'      => $item['Date'] ?? $item['published_at'] ?? null,
            'is_published'      => $item['is_published'] ?? false,
            // old
            'short_description' => $item['معلومات عن المقال'] ?? $item['short_description'] ?? null,
            'content'           => $item['المقال'] ?? $item['content'] ?? null,
            'author'            => $item['author'] ?? null,
            'blog_category_id'  => $item['blog_category_id'] ?? null,
            // new
            'product_id'        => $item['product_id'] ?? null,
            'keyword'           => $item['Keyword'] ?? $item['keyword'] ?? null,
            'keyword_strength'  => $item['قوة الكلمة المفتاحية'] ?? $item['keyword_strength'] ?? null,
            'search_intent'     => $item['البحث عن'] ?? $item['search_intent'] ?? null,
            'category_name'     => $item['تصنيف المقال'] ?? $item['category_name'] ?? null,
            'tags'              => $item['Tags'] ?? $item['tags'] ?? null,
            'meta_description'  => $item['وصف مختصر للمقال'] ?? $item['meta_description'] ?? null,
            'summary'           => $item['نبذه عن المقال'] ?? $item['summary'] ?? null,
            'content_html'      => $item['المقال_HTML'] ?? $item['content_html'] ?? null,
            'word_count'        => $item['عدد الكلمات'] ?? $item['word_count'] ?? null,
        ];
    }
}