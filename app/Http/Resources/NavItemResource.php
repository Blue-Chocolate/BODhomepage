<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NavItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'label'        => $this->label,
            'label_en'     => $this->label_en,
            'url'          => $this->url,
            'open_in_new_tab' => $this->open_in_new_tab,
            'children'     => NavItemResource::collection($this->whenLoaded('children')),
        ];
    }
}
