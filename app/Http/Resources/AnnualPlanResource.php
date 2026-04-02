<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnualPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'post_id'      => $this->post_id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'excerpt'      => $this->excerpt,
            'content_text' => $this->content_text,
            'link'         => $this->link,
            'status'       => $this->status,
            'category_id'  => $this->category_id,

            'featured_image' => [
                'url'           => $this->image_url,
                'file_name'     => $this->image_file_name,
                'drive_file_id' => $this->image_drive_file_id,
                'drive_link'    => $this->image_drive_link,
                'upload_status' => $this->image_upload_status,
            ],

            'content_image_1' => [
                'url'           => $this->content_image_1_url,
                'file_name'     => $this->content_image_1_file_name,
                'drive_file_id' => $this->content_image_1_drive_file_id,
                'drive_link'    => $this->content_image_1_drive_link,
                'upload_status' => $this->content_image_1_upload_status,
            ],

            'published_at' => $this->published_at?->toISOString(),
            'created_at'   => $this->created_at?->toISOString(),
            'updated_at'   => $this->updated_at?->toISOString(),
        ];
    }
}