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
        $assessment = Assessment::create([
            'title'       => 'نموذج تقييم الامتثال السنوي - منظومة والدة حلم الاجتماعية',
            'description' => 'الامتثال ليس تفتيشًا — الامتثال = استعداد + استدامة + ثقة',
            'period_year' => date('Y'),
            'is_active'   => true,
        ]);

        $axes = [
            [
                'title'                   => 'الحوكمة والامتثال النظامي',
                'recommendation_platform' => 'مسرعة أثر وريادة',
                'questions' => [
                    'وجود مجلس إدارة فعّال',
                    'اجتماعات دورية موثقة',
                    'لجان فاعلة',
                    'سياسات مكتوبة ومعتمدة',
                    'إدارة المخاطر',
                    'الامتثال للأنظمة',
                ],
            ],
            [
                'title'                   => 'القيادة والاستراتيجية',
                'recommendation_platform' => 'مسرعة أثر وريادة',
                'questions' => [
                    'رؤية ورسالة معتمدة',
                    'خطة استراتيجية محدثة',
                    'ارتباط الأهداف بالأثر',
                    'متابعة تنفيذ الاستراتيجية',
                    'قرارات مبنية على بيانات',
                ],
            ],
            [
                'title'                   => 'التخطيط والتشغيل',
                'recommendation_platform' => 'مسرعة أثر وريادة',
                'questions' => [
                    'خطة تشغيلية واضحة',
                    'إدارة المهام والأنشطة',
                    'تقارير تشغيلية دورية',
                    'وضوح الأدوار والمسؤوليات',
                    'كفاءة العمليات',
                ],
            ],
            [
                'title'                   => 'إدارة المشاريع',
                'recommendation_platform' => 'مختبرات حقق',
                'questions' => [
                    'إدارة المشاريع بمنهجية واضحة',
                    'اختبار الجاهزية قبل الإطلاق',
                    'تقارير مرحلية',
                    'إدارة المخاطر في المشاريع',
                    'قابلية التوسع',
                ],
            ],
            [
                'title'                   => 'قياس الأداء والأثر',
                'recommendation_platform' => 'منصة أداء',
                'questions' => [
                    'مؤشرات أداء واضحة',
                    'قياس دوري للأداء',
                    'تقارير أثر معتمدة',
                    'شهادات أداء',
                    'استخدام النتائج في القرار',
                ],
            ],
            [
                'title'                   => 'الإدارة المالية والاستدامة',
                'recommendation_platform' => 'مسرعة أثر وريادة',
                'questions' => [
                    'سياسات مالية معتمدة',
                    'شفافية مالية',
                    'تنويع مصادر الدخل',
                    'تقارير مالية منتظمة',
                    'جاهزية تمويلية',
                ],
            ],
            [
                'title'                   => 'الموارد البشرية وبناء القدرات',
                'recommendation_platform' => 'أكاديمية حقق',
                'questions' => [
                    'هيكل تنظيمي واضح',
                    'توصيف وظيفي',
                    'تقييم أداء الموظفين',
                    'خطط تطوير وتدريب',
                    'استقرار الفريق',
                ],
            ],
            [
                'title'                   => 'التقنية والتحول الرقمي',
                'recommendation_platform' => 'عباق وأثر 360',
                'questions' => [
                    'استخدام أنظمة معتمدة',
                    'توحيد البيانات',
                    'أمن المعلومات',
                    'أتمتة العمليات',
                    'استخدام الذكاء في القرار',
                ],
            ],
            [
                'title'                   => 'الاتصال المؤسسي والسرد',
                'recommendation_platform' => 'أكاديمية حقق',
                'questions' => [
                    'استراتيجية اتصال',
                    'شفافية التواصل',
                    'سرد الأثر',
                    'قنوات فعالة',
                    'تفاعل أصحاب المصلحة',
                ],
            ],
            [
                'title'                   => 'الإنسانية وأصحاب المصلحة',
                'recommendation_platform' => 'مسرعة أثر وريادة',
                'questions' => [
                    'تمكين المستفيدين',
                    'مراعاة العاملين',
                    'شمولية القرار',
                    'عدالة الإجراءات',
                    'أثر اجتماعي حقيقي',
                ],
            ],
        ];

        foreach ($axes as $axisOrder => $axisData) {
            $axis = AssessmentAxis::create([
                'assessment_id'           => $assessment->id,
                'title'                   => $axisData['title'],
                'recommendation_platform' => $axisData['recommendation_platform'],
                'order'                   => $axisOrder + 1,
                'is_active'               => true,
            ]);

            foreach ($axisData['questions'] as $qOrder => $question) {
                AssessmentQuestion::create([
                    'assessment_axis_id' => $axis->id,
                    'title'              => $question,
                    'order'              => $qOrder + 1,
                    'is_active'          => true,
                ]);
            }
        }
    }
}