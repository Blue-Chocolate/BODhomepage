<?php

// app/Http/Resources/HeaderSettingResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HeaderSettingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'logo' => [
                'image'   => $this->logo_image ? asset('storage/' . $this->logo_image) : null,
                'text'    => $this->logo_text,
                'text_en' => $this->logo_text_en,
                'url'     => $this->logo_url,
            ],
            'cta' => [
                'visible'     => $this->cta_visible,
                'text'        => $this->cta_text,
                'text_en'     => $this->cta_text_en,
                'url'         => $this->cta_url,
                'color'       => $this->cta_color,
                'open_in_new_tab' => $this->cta_new_tab,
            ],
            'behavior' => [
                'is_sticky'              => $this->is_sticky,
                'show_language_switcher' => $this->show_language_switcher,
            ],
        ];
    }

}
