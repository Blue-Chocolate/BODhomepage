<?php
// database/seeders/CaseStudySeeder.php

namespace Database\Seeders;

use App\Models\CaseStudy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CaseStudySeeder extends Seeder
{
    public function run(): void
    {
        $json = File::get(database_path('seeders/data/case_studies.json'));
        $records = json_decode($json, true);

        foreach ($records as $record) {
            CaseStudy::updateOrCreate(
                ['post_id' => $record['post_id']],
                [
                    'title'               => trim($record['title']),
                    'slug'                => is_numeric($record['slug'])
                                                ? (string) $record['slug']
                                                : $record['slug'],
                    'excerpt'             => $record['excerpt'] ?? null,
                    'content_text'        => $record['content_text'] ?? null,
                    'status'              => $record['status'] ?? 'publish',
                    'link'                => $record['link'] ?? null,
                    'image_url'           => $record['image_url'] ?? null,
                    'image_drive_file_id' => $record['image_drive_file_id'] ?? null,
                    'image_drive_link'    => $record['image_drive_link'] ?? null,
                    'image_file_name'     => $record['image_file_name'] ?? null,
                    'image_upload_status' => $record['image_upload_status'] ?? null,
                    'author_id'           => $record['author_id'] ?? null,
                    'author_name'         => $record['author_name'] ?? null,
                    'category_id'         => $record['categories'] ?? null,
                    'tags'                => $record['tags'] ?? null,
                    'reading_time'        => $record['reading_time'] ?? null,
                    'published_at'        => $record['date'] ?? null,
                ]
            );
        }

        $this->command->info('✅ CaseStudy seeder completed: ' . count($records) . ' records processed.');
    }
}