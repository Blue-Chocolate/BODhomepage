<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReleaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'row_number'          => $this->row_number,
            'edition_number'      => $this->edition_number,
            'post_id'             => $this->post_id,
            'date'                => $this->date,
            'modified'            => $this->modified,
            'status'              => $this->status,
            'link'                => $this->link,
            'title'               => $this->title,
            'excerpt'             => $this->excerpt,
            'content_text'        => $this->content_text,
            'author_id'           => $this->author_id,
            'author_name'         => $this->author_name,
            'image_url'           => $this->image_url,
            'image_drive_file_id' => $this->image_drive_file_id,
            'image_drive_link'    => $this->image_drive_link,
            'image_file_name'     => $this->image_file_name,
            'image_upload_status' => $this->image_upload_status,
            'categories'          => $this->categories,
            'tags'                => $this->tags,
            'slug'                => $this->slug,
            'reading_time'        => $this->reading_time,
            'created_at'          => $this->created_at?->toISOString(),
            'updated_at'          => $this->updated_at?->toISOString(),
        ];
    }
}