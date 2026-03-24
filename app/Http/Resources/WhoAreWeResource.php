<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\CoreValue;
class WhoAreWeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   public function toArray($request): array
{
    return [
        'title'       => ['ar' => $this->title,       'en' => $this->title_en],
        'description' => ['ar' => $this->description, 'en' => $this->description_en],
        'image'       => $this->image_path ? asset('storage/' . $this->image_path) : null,
        'vision'      => ['ar' => $this->vision,      'en' => $this->vision_en],
        'mission'     => ['ar' => $this->mission,     'en' => $this->mission_en],
        'core_values' => CoreValueResource::collection(CoreValue::getActive()),
    ];
}
}
