<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_question_id')->constrained()->cascadeOnDelete();

            // Score: 0 = not applicable, 1–5 = compliance scale
            $table->unsignedTinyInteger('score')->default(0);

            $table->text('notes')->nullable();       // evaluator notes per question
            $table->timestamps();

            $table->unique(['assessment_submission_id', 'assessment_question_id'], 'sub_answers_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_answers');
    }
};