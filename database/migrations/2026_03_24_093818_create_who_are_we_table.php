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
        Schema::create('who_are_we', function (Blueprint $table) {
    $table->id();
    $table->string('title')->nullable();
    $table->string('title_en')->nullable();
    $table->longText('description')->nullable();      // rich text
    $table->longText('description_en')->nullable();
    $table->string('image_path')->nullable();
    $table->text('vision')->nullable();               // بيان الرؤية
    $table->text('vision_en')->nullable();
    $table->text('mission')->nullable();              // بيان الرسالة
    $table->text('mission_en')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('who_are_we');
    }
};
