<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DigitalSolutionLink;

class DigitalSolutionLinkSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            [
                'label'           => 'الموقع الرسمي',
                'label_en'        => 'Official Website',
                'url'             => 'https://example.com',
                'open_in_new_tab' => true,
                'sort_order'      => 1,
                'is_active'       => true,
            ],
            [
                'label'           => 'التطبيق',
                'label_en'        => 'Mobile App',
                'url'             => 'https://app.example.com',
                'open_in_new_tab' => true,
                'sort_order'      => 2,
                'is_active'       => true,
            ],
            [
                'label'           => 'الدعم الفني',
                'label_en'        => 'Technical Support',
                'url'             => 'https://support.example.com',
                'open_in_new_tab' => false,
                'sort_order'      => 3,
                'is_active'       => true,
            ],
        ];

        foreach ($links as $link) {
            DigitalSolutionLink::create($link);
        }
    }
}