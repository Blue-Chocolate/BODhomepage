<?php
// database/seeders/BlogSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/blogs.json');
        $blogs = json_decode(file_get_contents($path), true);

        foreach ($blogs as $blog) {
            DB::table('blogs')->updateOrInsert(
                ['slug' => $blog['slug']],
                array_merge($blog, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}