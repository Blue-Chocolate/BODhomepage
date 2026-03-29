<?php 

// app/Http/Resources/SubServiceResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubServiceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'name'        => ['ar' => $this->name,        'en' => $this->name_en],
            'description' => ['ar' => $this->description, 'en' => $this->description_en],
        ];
    }
}