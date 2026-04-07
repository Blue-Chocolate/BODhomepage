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
                    'post_id'    => $item['post_id'] ?? null,
                    'row_number' => $item['row_number'] ?? null,
                ],
                [
                    'edition_number'       => $item['edition_number'] ?? null,
                    'date'                 => $item['date'] ?? null,
                    'modified'             => $item['modified'] ?? null,
                    'status'               => $item['status'] ?? 'publish',
                    'link'                 => $item['link'] ?? null,
                    'title'                => $item['title'] ?? null,
                    'excerpt'              => $item['excerpt'] ?? null,
                    'content_text'         => $item['content_text'] ?? null,
                    'author_id'            => $item['author_id'] ?? null,
                    'author_name'          => $item['author_name'] ?? null,
                    'image_url'            => $item['image_url'] ?? null,
                    'image_drive_file_id'  => $item['image_drive_file_id'] ?? null,
                    'image_drive_link'     => $item['image_drive_link'] ?? null,
                    'image_file_name'      => $item['image_file_name'] ?? null,
                    'image_upload_status'  => $item['image_upload_status'] ?? null,
                    'categories'           => $item['categories'] ?? null,
                    'tags'                 => $item['tags'] ?? null,
                    'slug'                 => $item['slug'] ?? null,
                    'reading_time'         => $item['reading_time'] ?? null,
                ]
            );
            $inserted++;
        }

        $this->command->info("✅ Release seeder completed: {$inserted} records processed.");
    }
}