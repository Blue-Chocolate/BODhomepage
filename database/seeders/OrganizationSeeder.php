<?php

namespace Database\Seeders;

use App\Models\Organization\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = [
            ['name' => 'جمعية البر الخيرية',          'type' => 'جمعية',  'contact_person' => 'أحمد محمد'],
            ['name' => 'مؤسسة الأمل للتنمية',         'type' => 'مؤسسة',  'contact_person' => 'سارة علي'],
            ['name' => 'مبادرة شباب المستقبل',         'type' => 'مبادرة', 'contact_person' => 'خالد عبدالله'],
            ['name' => 'جمعية رعاية الأسرة',           'type' => 'جمعية',  'contact_person' => 'فاطمة حسن'],
            ['name' => 'مؤسسة التطوع والعطاء',         'type' => 'مؤسسة',  'contact_person' => 'عمر يوسف'],
        ];

        foreach ($organizations as $org) {
            Organization::firstOrCreate(
                ['name' => $org['name']],
                array_merge($org, ['is_active' => true])
            );
        }
    }
}