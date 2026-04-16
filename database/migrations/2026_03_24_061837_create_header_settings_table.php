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
    Schema::create('header_settings', function (Blueprint $table) {
        $table->id();
        // Logo
        $table->string('logo_image')->nullable();
        $table->string('logo_text')->nullable();
        $table->string('logo_url')->default('/');
        $table->string('headline')->nullable();
        $table->string('subheadline')->nullable();
        $table->text('text')->nullable();
        $table->json('background_image')->nullable();
        $table->integer('organizations_count')->default(0);
        $table->integer('experience_years')->default(0);
        $table->integer('projects_count')->default(0);

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('header_settings');
    }
};
