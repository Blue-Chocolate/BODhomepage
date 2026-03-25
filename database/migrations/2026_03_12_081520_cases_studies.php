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
     Schema::create('case_studies', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('post_id')->unique()->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content_text')->nullable();
            $table->string('status')->default('publish');
            $table->string('link')->nullable();
            $table->string('image_url')->nullable();
            $table->string('image_drive_file_id')->nullable();
            $table->string('image_drive_link')->nullable();
            $table->string('image_file_name')->nullable();
            $table->string('image_upload_status')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->string('author_name')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->string('tags')->nullable();
            $table->string('reading_time')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_studies');
    }
};
