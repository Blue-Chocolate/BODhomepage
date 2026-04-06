<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
{
    \App\Models\BlogCategory::create([
        'name'        => 'عام',
        'slug'        => 'general',
        'description' => 'تدوينات عامة',
    ]);
}
}
