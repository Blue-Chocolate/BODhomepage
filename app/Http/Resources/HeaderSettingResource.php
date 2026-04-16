<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HeaderSettingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'logo' => [
                'image' => $this->logo_image ? asset('storage/' . $this->logo_image) : null,
                'text' => $this->logo_text,
                'url' => $this->logo_url,
            ],
            'headline' => $this->headline,
            'subheadline' => $this->subheadline,
            'text' => $this->text,
            'background_image' => $this->background_image ? asset('storage/' . $this->background_image) : null,
            'stats' => [
                'organizations_count' => $this->organizations_count,
                'experience_years' => $this->experience_years,
                'projects_count' => $this->projects_count,
            ],
        ];
    }
}