<?php

namespace Database\Seeders;

use App\Models\Compliance\Assessment;
use App\Models\Compliance\AssessmentAxis;
use App\Models\Compliance\AssessmentQuestion;
use Illuminate\Database\Seeder;

class ComplianceAssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $assessment = Assessment::firstOrCreate(
            ['period_year' => date('Y')],
            [
                'title'       => 'نموذج تقييم الامتثال السنوي - منظومة والدة حلم الاجتماعية',
                'description' => 'الامتثال ليس تفتيشًا — الامتثال = استعداد + استدامة + ثقة',
                'is_active'   => true,
            ]
        );

        // Each question: ['title' => string, 'weight' => 1-5]
        // Weight = أهمية السؤال داخل المحور (بيأثر على حساب متوسط المحور)
        $axes = [
            [
                'title'                   => 'الحوكمة والامتثال النظامي',
                'recommendation_platform' => 'مسرعة أثر وريادة',
                'questions' => [
                    ['title' => 'وجود مجلس إدارة فعّال',       'weight' => 5],
                    ['title' => 'اجتماعات دورية موثقة',         'weight' => 4],
                    ['title' => 'لجان فاعلة',                   'weight' => 3],
                    ['title' => 'سياسات مكتوبة ومعتمدة',        'weight' => 4],
                    ['title' => 'إدارة المخاطر',                'weight' => 4],
                    ['title' => 'الامتثال للأنظمة',             'weight' => 5],
                ],
            ],
            [
                'title'                   => 'القيادة والاستراتيجية',
                'recommendation_platform' => 'مسرعة أثر وريادة',
                'questions' => [
                    ['title' => 'رؤية ورسالة معتمدة',           'weight' => 4],
                    ['title' => 'خطة استراتيجية محدثة',         'weight' => 5],
                    ['title' => 'ارتباط الأهداف بالأثر',        'weight' => 4],
                    ['title' => 'متابعة تنفيذ الاستراتيجية',    'weight' => 4],
                    ['title' => 'قرارات مبنية على بيانات',      'weight' => 3],
                ],
            ],
            [
                'title'                   => 'التخطيط والتشغيل',
                'recommendation_platform' => 'مسرعة أثر وريادة',
                'questions' => [
                    ['title' => 'خطة تشغيلية واضحة',            'weight' => 5],
                    ['title' => 'إدارة المهام والأنشطة',         'weight' => 4],
                    ['title' => 'تقارير تشغيلية دورية',         'weight' => 3],
                    ['title' => 'وضوح الأدوار والمسؤوليات',     'weight' => 4],
                    ['title' => 'كفاءة العمليات',               'weight' => 4],
                ],
            ],
            [
                'title'                   => 'إدارة المشاريع',
                'recommendation_platform' => 'مختبرات حقق',
                'questions' => [
                    ['title' => 'إدارة المشاريع بمنهجية واضحة', 'weight' => 5],
                    ['title' => 'اختبار الجاهزية قبل الإطلاق',  'weight' => 4],
                    ['title' => 'تقارير مرحلية',                'weight' => 3],
                    ['title' => 'إدارة المخاطر في المشاريع',    'weight' => 4],
                    ['title' => 'قابلية التوسع',                'weight' => 4],
                ],
            ],
            [
                'title'                   => 'قياس الأداء والأثر',
                'recommendation_platform' => 'منصة أداء',
                'questions' => [
                    ['title' => 'مؤشرات أداء واضحة',            'weight' => 5],
                    ['title' => 'قياس دوري للأداء',             'weight' => 4],
                    ['title' => 'تقارير أثر معتمدة',            'weight' => 5],
                    ['title' => 'شهادات أداء',                  'weight' => 3],
                    ['title' => 'استخدام النتائج في القرار',    'weight' => 4],
                ],
            ],
            [
                'title'                   => 'الإدارة المالية والاستدامة',
                'recommendation_platform' => 'مسرعة أثر وريادة',
                'questions' => [
                    ['title' => 'سياسات مالية معتمدة',          'weight' => 5],
                    ['title' => 'شفافية مالية',                 'weight' => 5],
                    ['title' => 'تنويع مصادر الدخل',            'weight' => 4],
                    ['title' => 'تقارير مالية منتظمة',          'weight' => 4],
                    ['title' => 'جاهزية تمويلية',               'weight' => 3],
                ],
            ],
            [
                'title'                   => 'الموارد البشرية وبناء القدرات',
                'recommendation_platform' => 'أكاديمية حقق',
                'questions' => [
                    ['title' => 'هيكل تنظيمي واضح',             'weight' => 5],
                    ['title' => 'توصيف وظيفي',                  'weight' => 4],
                    ['title' => 'تقييم أداء الموظفين',          'weight' => 4],
                    ['title' => 'خطط تطوير وتدريب',             'weight' => 4],
                    ['title' => 'استقرار الفريق',               'weight' => 3],
                ],
            ],
            [
                'title'                   => 'التقنية والتحول الرقمي',
                'recommendation_platform' => 'عباق وأثر 360',
                'questions' => [
                    ['title' => 'استخدام أنظمة معتمدة',         'weight' => 4],
                    ['title' => 'توحيد البيانات',               'weight' => 4],
                    ['title' => 'أمن المعلومات',                'weight' => 5],
                    ['title' => 'أتمتة العمليات',               'weight' => 3],
                    ['title' => 'استخدام الذكاء في القرار',     'weight' => 4],
                ],
            ],
            [
                'title'                   => 'الاتصال المؤسسي والسرد',
                'recommendation_platform' => 'أكاديمية حقق',
                'questions' => [
                    ['title' => 'استراتيجية اتصال',             'weight' => 4],
                    ['title' => 'شفافية التواصل',               'weight' => 4],
                    ['title' => 'سرد الأثر',                    'weight' => 5],
                    ['title' => 'قنوات فعالة',                  'weight' => 3],
                    ['title' => 'تفاعل أصحاب المصلحة',         'weight' => 4],
                ],
            ],
            [
                'title'                   => 'الإنسانية وأصحاب المصلحة',
                'recommendation_platform' => 'مسرعة أثر وريادة',
                'questions' => [
                    ['title' => 'تمكين المستفيدين',             'weight' => 5],
                    ['title' => 'مراعاة العاملين',              'weight' => 4],
                    ['title' => 'شمولية القرار',                'weight' => 4],
                    ['title' => 'عدالة الإجراءات',              'weight' => 4],
                    ['title' => 'أثر اجتماعي حقيقي',           'weight' => 5],
                ],
            ],
        ];

        foreach ($axes as $axisOrder => $axisData) {
            $axis = AssessmentAxis::firstOrCreate(
                [
                    'assessment_id' => $assessment->id,
                    'title'         => $axisData['title'],
                ],
                [
                    'recommendation_platform' => $axisData['recommendation_platform'],
                    'order'                   => $axisOrder + 1,
                    'is_active'               => true,
                ]
            );

            foreach ($axisData['questions'] as $qOrder => $question) {
                AssessmentQuestion::firstOrCreate(
                    [
                        'assessment_axis_id' => $axis->id,
                        'title'              => $question['title'],
                    ],
                    [
                        'weight'    => $question['weight'],
                        'order'     => $qOrder + 1,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}