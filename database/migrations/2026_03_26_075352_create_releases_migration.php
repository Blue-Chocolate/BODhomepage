<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('releases', function (Blueprint $table) {
    $table->id();
    $table->integer('row_number')->nullable();
    $table->integer('edition_number')->nullable();
    $table->unsignedBigInteger('post_id')->nullable();
    $table->timestamp('date')->nullable();
    $table->string('categories')->nullable();
    $table->timestamp('modified')->nullable();
    $table->string('status')->default('publish');
    $table->string('link')->nullable();
    $table->string('title')->nullable();
    $table->text('excerpt')->nullable();
    $table->longText('content_text')->nullable();
    $table->unsignedBigInteger('author_id')->nullable();
    $table->string('author_name')->nullable();
    $table->string('image_url')->nullable();
    $table->string('image_drive_file_id')->nullable();
    $table->string('image_drive_link')->nullable();
    $table->string('image_file_name')->nullable();
    $table->string('image_upload_status')->nullable();
    $table->unsignedInteger('categories')->nullable();
    $table->string('tags')->nullable();
    $table->string('slug')->nullable();
    $table->string('reading_time')->nullable();
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};