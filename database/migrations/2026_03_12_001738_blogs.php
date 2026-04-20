<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('blogs', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('slug')->unique();
        $table->string('image_url')->nullable();
        $table->dateTime('published_at')->nullable();
        $table->boolean('is_published')->default(false);

        // old blogs
        $table->string('short_description')->nullable();
        $table->longText('content')->nullable();
        $table->string('author')->nullable();
        $table->foreignId('blog_category_id')
              ->nullable()
              ->constrained('blog_categories')
              ->onDelete('cascade');

        // new blogs
        $table->string('product_id')->nullable();
        $table->string('keyword')->nullable();
        $table->float('keyword_strength')->nullable();
        $table->string('search_intent')->nullable();
        $table->string('category_name')->nullable();
        $table->string('tags')->nullable();
        $table->string('meta_description')->nullable();
        $table->longText('summary')->nullable();
        $table->longText('content_html')->nullable();
        $table->unsignedInteger('word_count')->nullable();

        $table->timestamps();
    });
} 
public function down(): void
{
    Schema::dropIfExists('blogs');
}
};