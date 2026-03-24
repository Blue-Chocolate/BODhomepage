<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoreValueResource extends JsonResource
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
        'sort_order'  => $this->sort_order,
    ];
}
}
