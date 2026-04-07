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
            'button_text' => $this->button_text,
            'title_guess' => $this->title_guess,
            'card_text' => $this->card_text,
            'image_url' => $this->image_url,
        ];
    }
}