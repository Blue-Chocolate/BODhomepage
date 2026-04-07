<?php

namespace Database\Seeders;

use App\Models\Release;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ReleaseSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = database_path('seeders/data/releases.json');

        if (! File::exists($jsonPath)) {
            $this->command->warn("releases.json not found at: {$jsonPath}");
            return;
        }

        $data = json_decode(File::get($jsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Failed to parse releases.json: ' . json_last_error_msg());
            return;
        }

       $inserted = 0;

foreach ($data as $item) {
    Release::updateOrCreate(
        [
            'edition_number' => $item['edition_number'] ?? null,
            'row_number' => $item['row_number'] ?? null,
        ],
        [
            'file_url' => $item['file_url'] ?? null,
            'direct_download_url' => $item['direct_download_url'] ?? null,
            'button_text' => $item['button_text'] ?? null,
            'title_guess' => $item['title_guess'] ?? null,
            'card_text' => $item['card_text'] ?? null,
            'image_url' => $item['image_url'] ?? null,
        ]
    );

    $inserted++;
}


        $this->command->info("Seeded {$inserted} release(s) from releases.json.");
    }
}