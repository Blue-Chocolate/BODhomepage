<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annual_plans', function (Blueprint $table) {
            $table->id();

            $table->string('post_id')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content_text')->nullable();
            $table->string('link')->nullable();
            $table->enum('status', ['publish', 'draft', 'private'])->default('draft');
            $table->unsignedBigInteger('category_id')->nullable();

            // Main image
            $table->string('image_url')->nullable();
            $table->string('image_file_name')->nullable();
            $table->string('image_drive_file_id')->nullable();
            $table->string('image_drive_link')->nullable();
            $table->string('image_upload_status')->nullable();

            // Content image 1
            $table->string('content_image_1_url')->nullable();
            $table->string('content_image_1_file_name')->nullable();
            $table->string('content_image_1_drive_file_id')->nullable();
            $table->string('content_image_1_drive_link')->nullable();
            $table->string('content_image_1_upload_status')->nullable();

            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annual_plans');
    }
};