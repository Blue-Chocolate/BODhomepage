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
       Schema::create('news', function (Blueprint $table) {
    $table->id();
    // WordPress import fields
    $table->unsignedBigInteger('wp_post_id')->nullable()->unique();
    $table->integer('row_number')->nullable();

    // Content
    $table->string('title');
    $table->text('excerpt')->nullable();
    $table->longText('content_text')->nullable();
    $table->string('slug')->unique();
    $table->string('reading_time')->nullable();

    // Status & dates
    $table->enum('status', ['publish', 'draft', 'private', 'trash'])->default('draft');
    $table->timestamp('published_at')->nullable();
    $table->timestamp('modified_at')->nullable();

    // Author
    $table->unsignedBigInteger('author_id')->nullable();
    $table->string('author_name')->nullable();            // cached من WP

    // Image
    $table->string('image_path')->nullable();             // local storage
    $table->string('image_url')->nullable();              // original WP URL
    $table->string('image_drive_file_id')->nullable();    // Google Drive ID
    $table->string('image_drive_link')->nullable();
    $table->string('image_file_name')->nullable();
    $table->enum('image_upload_status', ['pending', 'uploaded', 'failed'])->default('pending');

    // Original WP link
    $table->string('link')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
