<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impact_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->year('year');

            // القسم الأول: البيانات الأساسية
            $table->decimal('social_inclusion',  5, 2);  // مؤشر الشمول الاجتماعي (0-100%)
            $table->decimal('service_quality',   5, 2);  // مؤشر جودة الخدمة (0-100%)
            $table->decimal('advocacy',          5, 2);  // مؤشر المناصرة (0-100%)
            $table->decimal('investment_value',  15, 2); // قيمة الاستثمار (ريال)
            $table->decimal('social_impact_value', 15, 2); // قيمة الأثر الاجتماعي (ريال)

            // المؤشرات المحتسبة تلقائياً
            $table->decimal('overall_impact_score', 5, 2)->nullable(); // (شمول+جودة+مناصرة)/3
            $table->decimal('sroi',                 8, 4)->nullable(); // قيمة الأثر ÷ قيمة الاستثمار
            $table->string('impact_classification')->nullable();       // ممتاز / جيد / يحتاج تحسين

            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['program_id', 'year'], 'impact_prog_year_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impact_reports');
    }
};