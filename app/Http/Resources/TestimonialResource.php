<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   public function toArray($request): array
{
    return [
        'id'           => $this->id,
        'name'         => ['ar' => $this->name,         'en' => $this->name_en],
        'organization' => ['ar' => $this->organization, 'en' => $this->organization_en],
        'quote'        => ['ar' => $this->quote,        'en' => $this->quote_en],
        'rating'       => $this->rating,
        'image'        => $this->image_path ? asset('storage/' . $this->image_path) : null,
    ];
}
}
