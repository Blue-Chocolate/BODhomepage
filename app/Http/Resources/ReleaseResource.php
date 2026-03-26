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
            'title_guess'         => $this->title_guess,
            'card_text'           => $this->card_text,
            'button_text'         => $this->button_text,
            'file_url'            => $this->file_url,
            'direct_download_url' => $this->direct_download_url,
            'image_url'           => $this->image_url,
            'created_at'          => $this->created_at?->toISOString(),
            'updated_at'          => $this->updated_at?->toISOString(),
        ];
    }
}