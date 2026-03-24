<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   public function toArray($request): array
{
    return [
        'id'   => $this->id,
        'name' => ['ar' => $this->name, 'en' => $this->name_en],
        'slug' => $this->slug,
    ];
}
}
