<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_submission_id')->constrained()->cascadeOnDelete();
            $table->enum('priority', ['high', 'medium', 'low']);
            $table->text('recommendation');
            $table->string('responsible_party')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_recommendations');
    }
};