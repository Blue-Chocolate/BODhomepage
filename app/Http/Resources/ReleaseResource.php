<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReleaseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'row_number' => $this->row_number,
            'edition_number' => $this->edition_number,
            'file_url' => $this->file_url,
            'direct_download_url' => $this->direct_download_url,
            'cover_image_url' => $this->cover_image_url,
            'image_drive_link' => $this->image_drive_link,
            'image_file_name' => $this->image_file_name,
            'image_drive_file_id' => $this->image_drive_file_id,
            'button_text' => $this->button_text,
            'title_guess' => $this->title_guess,
            'excerpt' => $this->excerpt,
            'card_text' => $this->card_text,
            'image_upload_status' => $this->image_upload_status,
            'image_url' => $this->image_url,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}