<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Service;
class ServiceSectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   public function toArray($request): array
{
    return [
        'title'    => ['ar' => $this->title,    'en' => $this->title_en],
        'subtitle' => ['ar' => $this->subtitle, 'en' => $this->subtitle_en],
        'services' => ServiceResource::collection(Service::getActive()),
    ];
}
}
