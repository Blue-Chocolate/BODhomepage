<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();

            // Who performed the evaluation
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();

            // Submission metadata
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'approved'])->default('draft');
            $table->text('evaluator_notes')->nullable();
            $table->text('management_decision')->nullable();
            $table->enum('management_action', [
                'approved',
                'approved_with_plan',
                'urgent_treatment',
                'reassess',
            ])->nullable();
            $table->unsignedTinyInteger('reassess_months')->nullable(); // if reassess chosen

            // Computed score cache (recalculated on answer save)
            $table->decimal('overall_score', 4, 2)->nullable();         // 0.00–5.00

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // One submission per org per assessment
            $table->unique(['assessment_id', 'organization_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_submissions');
    }
};