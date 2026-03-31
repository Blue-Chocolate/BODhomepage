<?php
// database/migrations/xxxx_xx_xx_create_strategic_plans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('strategic_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('post_id')->unique()->nullable()->comment('Original WP post ID');
            $table->string('title');
            $table->string('slug');
            $table->text('excerpt')->nullable();
            $table->longText('content_text')->nullable();
            $table->string('status')->default('publish'); // publish | draft | private
            $table->unsignedInteger('category_id')->nullable();
            $table->string('image_url')->nullable();
            $table->string('content_image_1')->nullable();
            $table->string('content_image_2')->nullable();
            // Drive metadata (keep for traceability, optional)
            $table->string('image_drive_file_id')->nullable();
            $table->string('content_image_1_drive_file_id')->nullable();
            $table->string('content_image_2_drive_file_id')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('strategic_plans');
    }
};