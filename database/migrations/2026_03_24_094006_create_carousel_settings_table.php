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
     Schema::create('carousel_settings', function (Blueprint $table) {
    $table->id();
    $table->string('section');                        // 'testimonials' أو 'success_stories'
    $table->boolean('auto_play')->default(true);
    $table->integer('auto_play_speed')->default(3000); // milliseconds
    $table->integer('slides_to_show')->default(3);
    $table->boolean('show_dots')->default(true);
    $table->boolean('show_arrows')->default(true);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carousel_settings');
    }
};
