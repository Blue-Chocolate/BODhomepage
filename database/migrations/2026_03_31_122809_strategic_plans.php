<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('strategic_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id')->unique();
            $table->timestamp('post_date')->nullable();
            $table->timestamp('post_modified')->nullable();
            $table->string('status', 50)->default('publish');
            $table->string('link')->nullable();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('content_text')->nullable();
            $table->string('image_url')->nullable();
            $table->string('content_image_1')->nullable();
            $table->string('content_image_2')->nullable();
            $table->string('execution_report')->nullable();
            $table->string('association_website')->nullable();

            // Drive metadata
            $table->string('image_drive_file_id')->nullable();
            $table->string('image_drive_link')->nullable();
            $table->string('image_file_name')->nullable();
            $table->string('image_upload_status', 50)->nullable();

            $table->string('content_image_1_drive_file_id')->nullable();
            $table->string('content_image_1_drive_link')->nullable();
            $table->string('content_image_1_file_name')->nullable();
            $table->string('content_image_1_upload_status', 50)->nullable();

            $table->string('content_image_2_drive_file_id')->nullable();
            $table->string('content_image_2_drive_link')->nullable();
            $table->string('content_image_2_file_name')->nullable();
            $table->string('content_image_2_upload_status', 50)->nullable();

            $table->unsignedInteger('categories')->nullable();
            $table->string('slug')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('strategic_plans');
    }
};