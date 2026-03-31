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
       Schema::create('procedural_evidences', function (Blueprint $table) {
    $table->id();

    $table->integer('row_number')->nullable();
    $table->unsignedBigInteger('post_id')->unique();

    $table->timestamp('date')->nullable();
    $table->timestamp('modified')->nullable();

    $table->string('status')->nullable();
    $table->string('link')->nullable();

    $table->string('title');
    $table->text('excerpt')->nullable();
    $table->longText('content_text')->nullable();

    $table->string('image_url')->nullable();
    $table->string('image_drive_file_id')->nullable();
    $table->string('image_drive_link')->nullable();
    $table->string('image_file_name')->nullable();
    $table->string('image_upload_status')->nullable();

    $table->integer('categories')->nullable();

    $table->string('slug')->unique();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedural_evidences');
    }
};
