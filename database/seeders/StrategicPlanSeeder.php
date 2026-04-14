<?php

namespace Database\Seeders;

use App\Models\StrategicPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class StrategicPlanSeeder extends Seeder
{
    /**
     * Place your JSON file at: database/data/strategic_plans.json
     *
     * The JSON must be an array of objects matching the structure
     * exported from WordPress (same shape as the sample record).
     */
    public function run(): void
    {
        $path = database_path('seeders/data/strategic_plans.json');

        if (! File::exists($path)) {
            $this->command->error("File not found: {$path}");
            return;
        }

        $records = json_decode(File::get($path), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Invalid JSON: ' . json_last_error_msg());
            return;
        }

        $this->command->info('Seeding ' . count($records) . ' strategic plans...');

        $bar = $this->command->getOutput()->createProgressBar(count($records));
        $bar->start();

        foreach ($records as $record) {
            StrategicPlan::updateOrCreate(
                ['post_id' => $record['post_id']],
                [
                    'post_date'    => $record['date']     ?? null,
                    'post_modified'=> $record['modified'] ?? null,
                    'status'       => $record['status']   ?? 'publish',
                    'link'         => $record['link']     ?? null,
                    'title'        => $record['title'],
                    'excerpt'      => $record['excerpt']  ?? null,
                    'content_text' => $record['content_text'] ?? null,
                    'image_url'    => $record['image_url'] ?? null,
                    'content_image_1' => $record['content_image_1'] ?? null,
                    'content_image_2' => $record['content_image_2'] ?? null,
                    'execution_report'    => $record['execution_report']     ?? null,
                    'association_website' => $record['association_website']  ?? null,

                    'image_drive_file_id'   => $record['image_drive_file_id']   ?? null,
                    'image_drive_link'      => $record['image_drive_link']      ?? null,
                    'image_file_name'       => $record['image_file_name']       ?? null,
                    'image_upload_status'   => $record['image_upload_status']   ?? null,

                    'content_image_1_drive_file_id'   => $record['content_image_1_drive_file_id']   ?? null,
                    'content_image_1_drive_link'      => $record['content_image_1_drive_link']      ?? null,
                    'content_image_1_file_name'       => $record['content_image_1_file_name']       ?? null,
                    'content_image_1_upload_status'   => $record['content_image_1_upload_status']   ?? null,

                    'content_image_2_drive_file_id'   => $record['content_image_2_drive_file_id']   ?? null,
                    'content_image_2_drive_link'      => $record['content_image_2_drive_link']      ?? null,
                    'content_image_2_file_name'       => $record['content_image_2_file_name']       ?? null,
                    'content_image_2_upload_status'   => $record['content_image_2_upload_status']   ?? null,

                    'categories' => isset($record['categories']) ? (int) $record['categories'] : null,
                    'slug'       => $record['slug'] ?? null,
                ]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Done.');
    }
}