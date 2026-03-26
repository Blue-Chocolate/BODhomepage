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
            $table->string('file_url')->nullable();
            $table->string('direct_download_url')->nullable();
            $table->string('button_text')->nullable();
            $table->string('title_guess')->nullable();
            $table->longText('card_text')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};