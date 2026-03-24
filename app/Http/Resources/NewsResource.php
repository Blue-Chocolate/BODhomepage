<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
{
    return [
        'id'           => $this->id,
        'title'        => $this->title,
        'slug'         => $this->slug,
        'excerpt'      => $this->excerpt,
        'content'      => $this->content_text,
        'image'        => $this->image,
        'reading_time' => $this->reading_time,
        'published_at' => $this->published_at?->toDateString(),
        'modified_at'  => $this->modified_at?->toDateString(),
        'author'       => $this->author_name,
        'categories'   => NewsCategoryResource::collection($this->whenLoaded('categories')),
        'tags'         => NewsCategoryResource::collection($this->whenLoaded('tags')),
    ];
}
}
