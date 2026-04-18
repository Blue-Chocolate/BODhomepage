<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Add these lines inside your existing DatabaseSeeder::run() method,
 * OR run them individually via artisan:
 *
 *   php artisan db:seed --class=OrganizationSeeder
 *   php artisan db:seed --class=ComplianceAssessmentSeeder
 */
class ComplianceDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            OrganizationSeeder::class,
            ComplianceAssessmentSeeder::class,
        ]);
    }
}