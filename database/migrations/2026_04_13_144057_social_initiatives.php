<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_initiatives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id')->unique();
            $table->string('title');
            $table->string('slug');
            $table->text('excerpt')->nullable();
            $table->longText('content_text')->nullable();
            $table->string('image_url')->nullable();
            $table->string('content_image_1')->nullable();
            $table->string('image_drive_file_id')->nullable();
            $table->string('image_drive_link')->nullable();
            $table->string('image_file_name')->nullable();
            $table->string('image_upload_status')->nullable();
            $table->string('content_image_1_drive_file_id')->nullable();
            $table->string('content_image_1_drive_link')->nullable();
            $table->string('content_image_1_file_name')->nullable();
            $table->string('content_image_1_upload_status')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('status')->default('publish');
            $table->string('link')->nullable();
            $table->timestamp('post_date')->nullable();
            $table->timestamp('post_modified')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_initiatives');
    }
};