<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomepageHeroSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'title'                 => $this->title,
            'description'           => $this->description,
            'background_image'      => $this->background_image,
            'background_video_url'  => $this->background_video_url,
            'text'                  => $this->text,
            'subtext'               => $this->subtext,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}