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
       Schema::create('service_section_settings', function (Blueprint $table) {
    $table->id();
    $table->string('title')->nullable();
    $table->string('title_en')->nullable();
    $table->text('subtitle')->nullable();
    $table->text('subtitle_en')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_section_settings');
    }
};
