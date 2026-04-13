<?php

namespace Database\Seeders;

use App\Models\SocialInitiative;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class SocialInitiativeSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/social_initiatives.json');

        if (! File::exists($path)) {
            $this->command->error("Seed file not found: {$path}");
            return;
        }

        $records = json_decode(File::get($path), true);

        foreach ($records as $record) {
            SocialInitiative::updateOrCreate(
                ['post_id' => $record['post_id']],
                [
                    'title'                            => $record['title'],
                    'slug'                             => urldecode($record['slug']),
                    'excerpt'                          => $record['excerpt'] ?? null,
                    'content_text'                     => $record['content_text'] ?? null,
                    'image_url'                        => $record['image_url'] ?? null,
                    'content_image_1'                  => $record['content_image_1'] ?? null,
                    'image_drive_file_id'              => $record['image_drive_file_id'] ?? null,
                    'image_drive_link'                 => $record['image_drive_link'] ?? null,
                    'image_file_name'                  => $record['image_file_name'] ?? null,
                    'image_upload_status'              => $record['image_upload_status'] ?? null,
                    'content_image_1_drive_file_id'    => $record['content_image_1_drive_file_id'] ?? null,
                    'content_image_1_drive_link'       => $record['content_image_1_drive_link'] ?? null,
                    'content_image_1_file_name'        => $record['content_image_1_file_name'] ?? null,
                    'content_image_1_upload_status'    => $record['content_image_1_upload_status'] ?? null,
                    'category_id'                      => $record['categories'] ?? null,
                    'status'                           => $record['status'] ?? 'publish',
                    'link'                             => $record['link'] ?? null,
                    'post_date'                        => $record['date'] ?? null,
                    'post_modified'                    => $record['modified'] ?? null,
                ]
            );
        }

        $this->command->info('SocialInitiative seeded: ' . count($records) . ' records.');
    }
}