<?php

// app/Http/Resources/FooterResource.php
namespace App\Http\Resources;

use App\Models\FooterNavGroup;
use App\Models\LegalLink;
use App\Models\SocialLink;
use Illuminate\Http\Resources\Json\JsonResource;

class FooterResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'logo'    => $this->logo_image ? asset('storage/' . $this->logo_image) : null,
            'tagline' => ['ar' => $this->tagline, 'en' => $this->tagline_en],

            'contact' => [
                'email'   => $this->contact_email,
                'phone'   => $this->contact_phone,
                'address' => ['ar' => $this->contact_address, 'en' => $this->contact_address_en],
                'map_url' => $this->contact_map_url,
            ],

            'nav_groups' => FooterNavGroup::getActive()->map(fn($group) => [
                'id'    => $group->id,
                'title' => ['ar' => $group->title, 'en' => $group->title_en],
                'links' => $group->links->map(fn($link) => [
                    'id'             => $link->id,
                    'label'          => ['ar' => $link->label, 'en' => $link->label_en],
                    'url'            => $link->url,
                    'open_in_new_tab'=> $link->open_in_new_tab,
                ]),
            ]),

            'social_links' => SocialLink::getActive()->map(fn($s) => [
                'platform' => $s->platform,
                'label'    => $s->label,
                'url'      => $s->url,
                'icon'     => $s->icon,
            ]),

            'newsletter' => [
                'enabled'      => $this->newsletter_enabled,
                'title'        => ['ar' => $this->newsletter_title,        'en' => $this->newsletter_title_en],
                'placeholder'  => ['ar' => $this->newsletter_placeholder,  'en' => $this->newsletter_placeholder_en],
                'button_text'  => ['ar' => $this->newsletter_button_text,  'en' => $this->newsletter_button_text_en],
            ],

            'legal_links' => LegalLink::getActive()->map(fn($l) => [
                'label'          => ['ar' => $l->label, 'en' => $l->label_en],
                'url'            => $l->url,
                'open_in_new_tab'=> $l->open_in_new_tab,
            ]),

            'copyright'     => ['ar' => $this->copyright_text, 'en' => $this->copyright_text_en],
            'back_to_top'   => $this->back_to_top_enabled,
        ];
    }
}