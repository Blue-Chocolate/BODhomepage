<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->enum('type', ['government', 'foundation', 'non_profit']);
            $table->string('liscense_number');
            $table->timestamp('evaluation_date')->nullable();
            $table->integer('evaluation_duration')->nullable();
            $table->float('evaluation_score')->nullable();
            $table->string('evaluator_name')->nullable();
            $table->string('evaluation_team')->nullable();
            $table->text('evaluation_notes')->nullable();
            $table->string('representative_name')->nullable();
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
