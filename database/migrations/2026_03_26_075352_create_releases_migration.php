<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('releases', function (Blueprint $table) {
            $table->id();
            $table->integer('row_number')->nullable();
            $table->integer('edition_number')->nullable();
            $table->string('file_url')->nullable();
            $table->string('direct_download_url')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->string('image_drive_link')->nullable();
            $table->string('image_file_name')->nullable();
            $table->string('image_drive_file_id')->nullable();
            $table->string('button_text')->nullable();
            $table->string('title_guess')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('card_text')->nullable();
            $table->string('image_upload_status')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('releases');
    }
};