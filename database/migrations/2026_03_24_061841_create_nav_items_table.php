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
    Schema::create('nav_items', function (Blueprint $table) {
        $table->id();
        $table->string('label');
        $table->string('label_en')->nullable();
        $table->string('url')->default('#');
        $table->boolean('open_in_new_tab')->default(false);
        $table->boolean('is_active')->default(true);
        $table->integer('sort_order')->default(0);
        $table->foreignId('parent_id')
              ->nullable()
              ->constrained('nav_items')
              ->nullOnDelete();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nav_items');
    }
};
