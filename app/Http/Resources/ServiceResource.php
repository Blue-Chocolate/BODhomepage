<?php

// app/Http/Resources/ServiceResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'title'        => ['ar' => $this->title,       'en' => $this->title_en],
            'description'  => ['ar' => $this->description, 'en' => $this->description_en],
            'sub_services' => SubServiceResource::collection(
                $this->whenLoaded('activeSubServices')
            ),
        ];
    }
}   
