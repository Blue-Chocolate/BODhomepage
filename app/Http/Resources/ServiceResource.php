<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
{
    return [
        'id'          => $this->id,
        'title'       => ['ar' => $this->title,       'en' => $this->title_en],
        'description' => ['ar' => $this->description, 'en' => $this->description_en],
        'icon'        => $this->icon,
        'image'       => $this->image_path ? asset('storage/' . $this->image_path) : null,
        'cta'         => [
            'text'    => ['ar' => $this->cta_text, 'en' => $this->cta_text_en],
            'url'     => $this->cta_url,
        ],
    ];
}
}
