<?php

// database/seeders/NewsSeeder.php
namespace Database\Seeders;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        // تنظيف الجداول قبل الـ seed
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('news_category')->truncate();
        DB::table('news')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = $this->getData();

        foreach ($data as $item) {
            // تخطي السجلات بدون title
            if (empty(trim($item['title'] ?? ''))) {
                continue;
            }

            // تنظيف الـ title من HTML entities
            $title = html_entity_decode($item['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8');

            $news = News::create([
                'wp_post_id'           => $item['post_id'],
                'row_number'           => $item['row_number'],
                'title'                => $title,
                'excerpt'              => $item['excerpt'] ?? null,
                'content_text'         => $item['content_text'] ?? null,
                'slug'                 => $item['slug'],
                'reading_time'         => !empty($item['reading_time']) ? $item['reading_time'] : null,
                'status'               => $item['status'],
                'published_at'         => $item['date'],
                'modified_at'          => $item['modified'] ?? $item['date'],
                'author_id'            => $item['author_id'],
                'author_name'          => $item['author_name'],
                'image_url'            => $item['image_url'] ?? null,
                'image_drive_file_id'  => $item['image_drive_file_id'] ?? null,
                'image_drive_link'     => $item['image_drive_link'] ?? null,
                'image_file_name'      => $item['image_file_name'] ?? null,
                'image_upload_status'  => $item['image_upload_status'] ?? 'pending',
                'link'                 => $item['link'] ?? null,
            ]);

            // ربط الكاتيجوري — أحياناً "6, 8" وأحياناً integer
            $categoryIds = $this->parseCategoryIds($item['categories'] ?? '');

            foreach ($categoryIds as $wpTermId) {
                $category = NewsCategory::firstOrCreate(
                    ['wp_term_id' => $wpTermId],
                    [
                        'name' => 'تصنيف ' . $wpTermId,
                        'slug' => 'category-' . $wpTermId,
                    ]
                );

                $news->categories()->syncWithoutDetaching([$category->id]);
            }
        }

        $this->command->info('✅ تم seed ' . News::count() . ' خبر بنجاح');
    }

    /**
     * تحويل categories إلى array من IDs
     * يقبل: 6 أو "6" أو "6, 8" أو "6, 5"
     */
    private function parseCategoryIds(mixed $categories): array
    {
        if (empty($categories) && $categories !== 0) {
            return [];
        }

        return array_filter(
            array_map('intval',
                array_map('trim',
                    explode(',', (string) $categories)
                )
            ),
            fn($id) => $id > 0
        );
    }

    private function getData(): array
    {
        $jsonPath = database_path('seeders/data/news.json');

        if (file_exists($jsonPath)) {
            return json_decode(file_get_contents($jsonPath), true);
        }

        // fallback: البيانات inline
        return $this->inlineData();
    }

    private function inlineData(): array
    {
        return json_decode('[
  {
    "row_number": 4,
    "post_id": 6066,
    "date": "2026-03-04 8:51:35",
    "modified": "2026-03-04 8:52:01",
    "status": "publish",
    "link": "https://bod.com.sa/blog/2026/03/04/%d9%88%d8%b1%d8%b4%d8%a9-%d8%aa%d9%82%d9%8a%d9%8a%d9%85-%d9%82%d8%af%d8%b1%d8%a7%d8%aa-%d8%a7%d9%84%d9%85%d9%86%d8%b8%d9%85%d8%a9-%d9%81%d9%8a-%d8%a7%d9%84%d8%a7%d8%b3%d8%aa%d8%af%d8%a7%d9%85%d8%a9/",
    "title": "\u0648\u0631\u0634\u0629 \u062a\u0642\u064a\u064a\u0645 \u0642\u062f\u0631\u0627\u062a \u0627\u0644\u0645\u0646\u0638\u0645\u0629 \u0641\u064a \u0627\u0644\u0627\u0633\u062a\u062f\u0627\u0645\u0629 \u0627\u0644\u0645\u0627\u0644\u064a\u0629 _ \u062c\u0645\u0639\u064a\u0629 \u0639\u0632\u0648\u0629 \u0644\u0644\u062d\u0645\u0627\u064a\u0629 \u0627\u0644\u0623\u0633\u0631\u064a\u0629",
    "excerpt": "\u062c\u0627\u0646\u0628 \u0645\u0646 \u0648\u0631\u0634\u0629 \u062a\u0642\u064a\u064a\u0645 \u0642\u062f\u0631\u0627\u062a \u0627\u0644\u0645\u0646\u0638\u0645\u0629 \u0641\u064a \u0627\u0644\u0627\u0633\u062a\u062f\u0627\u0645\u0629 \u0627\u0644\u0645\u0627\u0644\u064a\u0629 \u0627\u0644\u0645\u0642\u062f\u0645\u0629 \u0644\u0640 \u062c\u0645\u0639\u064a\u0629 \u0639\u0632\u0648\u0629 \u0644\u0644\u062d\u0645\u0627\u064a\u0629 \u0627\u0644\u0623\u0633\u0631\u064a\u0629 \u060c \u0636\u0645\u0646 \u0645\u062c\u0627\u0644 \u062a\u0646\u0645\u064a\u0629 \u0627\u0644\u0645\u0648\u0627\u0631\u062f [\u2026]",
    "content_text": "\u062c\u0627\u0646\u0628 \u0645\u0646 \u0648\u0631\u0634\u0629 \u062a\u0642\u064a\u064a\u0645 \u0642\u062f\u0631\u0627\u062a \u0627\u0644\u0645\u0646\u0638\u0645\u0629 \u0641\u064a \u0627\u0644\u0627\u0633\u062a\u062f\u0627\u0645\u0629 \u0627\u0644\u0645\u0627\u0644\u064a\u0629 \u0627\u0644\u0645\u0642\u062f\u0645\u0629 \u0644\u0640 \u062c\u0645\u0639\u064a\u0629 \u0639\u0632\u0648\u0629 \u0644\u0644\u062d\u0645\u0627\u064a\u0629 \u0627\u0644\u0623\u0633\u0631\u064a\u0629 \u060c \u0636\u0645\u0646 \u0645\u062c\u0627\u0644 \u062a\u0646\u0645\u064a\u0629 \u0627\u0644\u0645\u0648\u0627\u0631\u062f \u0627\u0644\u0645\u0627\u0644\u064a\u0629 \u0636\u0645\u0646 \u0645\u0634\u0631\u0648\u0639 \u0627\u0644\u0627\u062d\u062a\u0636\u0627\u0646 \u0627\u0644\u0643\u0627\u0645\u0644 \u0627\u0644\u062a\u0627\u0628\u0639 \u0644\u0640 \u0645\u0624\u0633\u0633\u0629 \u0627\u0644\u0645\u0644\u0643 \u062e\u0627\u0644\u062f.",
    "author_id": 1,
    "author_name": "admin",
    "image_url": "https://bod.com.sa/wp-content/uploads/2024/03/44.jpg",
    "image_drive_file_id": "1puhG6YXjy3cby1iD80o-kqn-HVet1GbH",
    "image_drive_link": "https://drive.google.com/file/d/1puhG6YXjy3cby1iD80o-kqn-HVet1GbH/view?usp=drivesdk",
    "image_file_name": "6066-\u0648\u0631\u0634\u0629 \u062a\u0642\u064a\u064a\u0645 \u0642\u062f\u0631\u0627\u062a \u0627\u0644\u0645\u0646\u0638\u0645\u0629 \u0641\u064a \u0627\u0644\u0627\u0633\u062a\u062f\u0627\u0645\u0629 \u0627\u0644\u0645\u0627\u0644\u064a\u0629 _ \u062c\u0645\u0639\u064a\u0629 \u0639\u0632\u0648\u0629 \u0644\u0644\u062d\u0645\u0627\u064a\u0629 \u0627\u0644\u0623\u0633\u0631\u064a\u0629.jpg",
    "image_upload_status": "uploaded",
    "categories": 6,
    "tags": "",
    "slug": "\u0648\u0631\u0634\u0629-\u062a\u0642\u064a\u064a\u0645-\u0642\u062f\u0631\u0627\u062a-\u0627\u0644\u0645\u0646\u0638\u0645\u0629-\u0641\u064a-\u0627\u0644\u0627\u0633\u062a\u062f\u0627\u0645\u0629",
    "reading_time": "\u062f\u0642\u064a\u0642\u0629 \u0648\u0627\u062d\u062f\u0629"
  }
]', true);
    }
}