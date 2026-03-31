<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\File;
use App\Models\ProceduralEvidence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ProceduralEvidenceSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/procedural_evidences.json');

        if (!File::exists($path)) {
            $this->command->error('JSON file not found');
            return;
        }

        $data = json_decode(File::get($path), true);

        foreach ($data as $item) {
            ProceduralEvidence::updateOrCreate(
                ['post_id' => $item['post_id']],
                [
                    'row_number' => $item['row_number'] ?? null,
                    'date' => $item['date'] ?? null,
                    'modified' => $item['modified'] ?? null,
                    'status' => $item['status'] ?? null,
                    'link' => $item['link'] ?? null,
                    'title' => $item['title'] ?? null,
                    'excerpt' => $item['excerpt'] ?? null,
                    'content_text' => $item['content_text'] ?? null,
                    'image_url' => $item['image_url'] ?? null,
                    'image_drive_file_id' => $item['image_drive_file_id'] ?? null,
                    'image_drive_link' => $item['image_drive_link'] ?? null,
                    'image_file_name' => $item['image_file_name'] ?? null,
                    'image_upload_status' => $item['image_upload_status'] ?? null,
                    'categories' => $item['categories'] ?? null,
                    'slug' => $item['slug'] ?? null,
                ]
            );
        }
    }
}