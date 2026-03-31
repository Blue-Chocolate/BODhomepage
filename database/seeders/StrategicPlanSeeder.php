<?php
// database/seeders/StrategicPlanSeeder.php

namespace Database\Seeders;

use App\Models\StrategicPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class StrategicPlanSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/strategic_plans.json');

        if (! File::exists($path)) {
            $this->command->warn("Data file not found: {$path}");
            return;
        }

        $records = json_decode(File::get($path), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Invalid JSON in strategic_plans.json');
            return;
        }

        foreach ($records as $record) {
            StrategicPlan::updateOrCreate(
                ['post_id' => $record['post_id']],
                [
                    'title'                          => $record['title'],
                    'slug'                           => Str::slug($record['title']) ?: urldecode($record['slug']),
                    'excerpt'                        => $record['excerpt'] ?? null,
                    'content_text'                   => $record['content_text'] ?? null,
                    'status'                         => $record['status'] ?? 'publish',
                    'category_id'                    => $record['categories'] ?? null,
                    'image_url'                      => $record['image_url'] ?? null,
                    'content_image_1'                => $record['content_image_1'] ?? null,
                    'content_image_2'                => $record['content_image_2'] ?? null,
                    'image_drive_file_id'            => $record['image_drive_file_id'] ?? null,
                    'content_image_1_drive_file_id'  => $record['content_image_1_drive_file_id'] ?? null,
                    'content_image_2_drive_file_id'  => $record['content_image_2_drive_file_id'] ?? null,
                    'published_at'                   => $record['date'] ?? null,
                ]
            );
        }

        $this->command->info('Strategic plans seeded: ' . count($records) . ' records.');
    }
}