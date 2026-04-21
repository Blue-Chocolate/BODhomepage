<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_governance_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->year('year');

            $table->decimal('pillar_impact', 8, 2)->default(0);
            $table->decimal('pillar_integrity', 8, 2)->default(0);
            $table->decimal('pillar_empowerment', 8, 2)->default(0);
            $table->decimal('pillar_innovation', 8, 2)->default(0);
            $table->decimal('pillar_capacity', 8, 2)->default(0);
            $table->decimal('pillar_sustainability', 8, 2)->default(0);
            $table->decimal('overall_score', 8, 2)->default(0);

            $table->decimal('total_budget', 15, 2)->default(0);
            $table->unsignedBigInteger('total_beneficiaries')->default(0);
            $table->decimal('budget_variance', 15, 2)->default(0);
            $table->decimal('cost_per_beneficiary', 15, 2)->default(0);

            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();

            $table->unique(['program_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_governance_scores');
    }
};