<?php

namespace Database\Seeders;

use App\Modules\AnnualPlans\Models\AnnualPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class AnnualPlanSeeder extends Seeder
{
    /**
     * Path to the JSON data file.
     * Place your JSON file at: database/data/annual_plans.json
     */
    protected string $dataPath = 'seeders/data/annual_plans.json';

    public function run(): void
    {
        $absolutePath = base_path($this->dataPath);

        if (! File::exists($absolutePath)) {
            $this->command->error("Data file not found at: {$absolutePath}");
            $this->command->info('Create the file and place your JSON array there.');
            return;
        }

        $json = File::get($absolutePath);
        $records = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Invalid JSON: ' . json_last_error_msg());
            return;
        }

        // Support both a single object and an array of objects
        if (isset($records['post_id'])) {
            $records = [$records];
        }

        $this->command->info('Seeding ' . count($records) . ' annual plan(s)...');

        $inserted = 0;
        $skipped  = 0;

        foreach ($records as $row) {
            $exists = AnnualPlan::where('post_id', $row['post_id'])->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            AnnualPlan::create([
                'post_id'                          => $row['post_id'],
                'title'                            => $row['title'],
                'slug'                             => $row['slug'],
                'excerpt'                          => $row['excerpt'] ?? null,
                'content_text'                     => $row['content_text'] ?? null,
                'link'                             => $row['link'] ?? null,
                'status'                           => $row['status'] ?? 'publish',
                'category_id'                      => $row['categories'] ?? null,

                'image_url'                        => $row['image_url'] ?? null,
                'image_file_name'                  => $row['image_file_name'] ?? null,
                'image_drive_file_id'              => $row['image_drive_file_id'] ?? null,
                'image_drive_link'                 => $row['image_drive_link'] ?? null,
                'image_upload_status'              => $row['image_upload_status'] ?? 'pending',

                'content_image_1_url'              => $row['content_image_1'] ?? null,
                'content_image_1_file_name'        => $row['content_image_1_file_name'] ?? null,
                'content_image_1_drive_file_id'    => $row['content_image_1_drive_file_id'] ?? null,
                'content_image_1_drive_link'       => $row['content_image_1_drive_link'] ?? null,
                'content_image_1_upload_status'    => $row['content_image_1_upload_status'] ?? 'pending',

                'published_at'                     => $row['date'] ?? null,
            ]);

            $inserted++;
        }

        $this->command->info("Done. Inserted: {$inserted}, Skipped (duplicate): {$skipped}");
    }
}