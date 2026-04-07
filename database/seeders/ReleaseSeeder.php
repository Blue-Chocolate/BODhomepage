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

        // Handle both single object and array of objects
        if (isset($data['row_number'])) {
            // Single object case
            $data = [$data];
        }

        $inserted = 0;
        $skipped = 0;

        foreach ($data as $item) {
            // Skip if no edition_number or row_number (required for updateOrCreate)
            if (empty($item['edition_number']) && empty($item['row_number'])) {
                $this->command->warn("Skipping item without edition_number or row_number: " . json_encode($item));
                $skipped++;
                continue;
            }

            Release::updateOrCreate(
                [
                    'edition_number' => $item['edition_number'] ?? null,
                    'row_number' => $item['row_number'] ?? null,
                ],
                [
                    'file_url' => $item['file_url'] ?? null,
                    'direct_download_url' => $item['direct_download_url'] ?? null,
                    'cover_image_url' => $item['cover_image_url'] ?? null,
                    'image_drive_link' => $item['image_drive_link'] ?? null,
                    'image_file_name' => $item['image_file_name'] ?? null,
                    'image_drive_file_id' => $item['image_drive_file_id'] ?? null,
                    'button_text' => $item['button_text'] ?? null,
                    'title_guess' => $item['title_guess'] ?? null,
                    'excerpt' => $item['excerpt'] ?? null,
                    'card_text' => $item['card_text'] ?? null,
                    'image_upload_status' => $item['image_upload_status'] ?? null,
                    'image_url' => $item['image_url'] ?? null,
                ]
            );

            $inserted++;
        }

        $this->command->info("Seeded {$inserted} release(s) from releases.json.");
        if ($skipped > 0) {
            $this->command->warn("Skipped {$skipped} item(s) due to missing required fields.");
        }
    }
}