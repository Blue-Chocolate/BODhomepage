<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialInitiativeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                            => $this->id,
            'post_id'                       => $this->post_id,
            'title'                         => $this->title,
            'slug'                          => $this->slug,
            'excerpt'                       => $this->excerpt,
            'content_text'                  => $this->content_text,
            'image_url'                     => $this->image_url,
            'content_image_1'               => $this->content_image_1,
            'image_drive_link'              => $this->image_drive_link,
            'content_image_1_drive_link'    => $this->content_image_1_drive_link,
            'category_id'                   => $this->category_id,
            'status'                        => $this->status,
            'link'                          => $this->link,
            'post_date'                     => $this->post_date?->toDateTimeString(),
            'post_modified'                 => $this->post_modified?->toDateTimeString(),
            'created_at'                    => $this->created_at?->toDateTimeString(),
        ];
    }
}