<?php

namespace Database\Seeders;

use App\Models\Organization\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = [
            [
                'name'                => 'جمعية البر الخيرية',
                'type'                => 'non_profit',
                'liscense_number'     => 'NP-2024-001',
                'representative_name' => 'أحمد محمد',
                'approval_status'     => 'approved',
                'approved_at'         => now(),
            ],
            [
                'name'                => 'مؤسسة الأمل للتنمية',
                'type'                => 'foundation',
                'liscense_number'     => 'FN-2024-002',
                'representative_name' => 'سارة علي',
                'approval_status'     => 'approved',
                'approved_at'         => now(),
            ],
            [
                'name'                => 'مبادرة شباب المستقبل',
                'type'                => 'non_profit',
                'liscense_number'     => 'NP-2024-003',
                'representative_name' => 'خالد عبدالله',
                'approval_status'     => 'pending',
            ],
            [
                'name'                => 'جمعية رعاية الأسرة',
                'type'                => 'non_profit',
                'liscense_number'     => 'NP-2024-004',
                'representative_name' => 'فاطمة حسن',
                'approval_status'     => 'approved',
                'approved_at'         => now(),
            ],
            [
                'name'                => 'مؤسسة التطوع والعطاء',
                'type'                => 'foundation',
                'liscense_number'     => 'FN-2024-005',
                'representative_name' => 'عمر يوسف',
                'approval_status'     => 'pending',
            ],
        ];

        foreach ($organizations as $org) {
            Organization::firstOrCreate(
                ['liscense_number' => $org['liscense_number']],
                $org
            );
        }
    }
}