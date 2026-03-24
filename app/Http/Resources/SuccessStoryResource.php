<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuccessStoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   public function toArray($request): array
{
    return [
        'id'      => $this->id,
        'title'   => ['ar' => $this->title,   'en' => $this->title_en],
        'content' => ['ar' => $this->content, 'en' => $this->content_en],
        'image'   => $this->image_path ? asset('storage/' . $this->image_path) : null,
        'video'   => [
            'url'   => $this->video_url,
            'embed' => $this->video_embed,
        ],
    ];
}
}
