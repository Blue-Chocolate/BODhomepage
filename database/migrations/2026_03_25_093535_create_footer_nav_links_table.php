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
       Schema::create('footer_nav_links', function (Blueprint $table) {
    $table->id();
    $table->foreignId('footer_nav_group_id')
          ->constrained()
          ->cascadeOnDelete();
    $table->string('label');
    $table->string('label_en')->nullable();
    $table->string('url');
    $table->boolean('open_in_new_tab')->default(false);
    $table->integer('sort_order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_nav_links');
    }
};
