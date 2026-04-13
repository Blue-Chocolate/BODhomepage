<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    $this->call([
        
        UserSeeder::class,
    BlogCategorySeeder::class,
        BlogSeeder::class,
        NewsSeeder::class,
        CaseStudySeeder::class,
        ReleaseSeeder::class,
        StrategicPlanSeeder::class,
        ProceduralEvidenceSeeder::class,
        AnnualPlanSeeder::class,
        DigitalSolutionLinkSeeder ::class,
        SocialInitiativeSeeder::class,

        
    ]);
}
}
