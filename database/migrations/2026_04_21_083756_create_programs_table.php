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
        Schema::create('programs', function (Blueprint $table) {
                 $table->id();
                    $table->foreignId('organization_id')->constrained()->onDelete('cascade');
                    $table->string('name');
                    $table->enum('status', ['completed', 'in_progress', 'planning', 'suspended']);
                    $table->decimal('total_actual_cost', 15, 2)->nullable();
                    $table->integer('execution_duration')->nullable(); // in months
                    $table->decimal('resource_efficiency', 5, 2)->nullable(); // ratio or percentage
                    $table->decimal('cost_per_beneficiary', 15, 2)->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
