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
       // xxxx_create_footer_settings_table.php
Schema::create('footer_settings', function (Blueprint $table) {
    $table->id();
    // Logo & Tagline
    $table->string('logo_image')->nullable();
    $table->text('tagline')->nullable();
    $table->text('tagline_en')->nullable();
    // Contact
    $table->string('contact_email')->nullable();
    $table->string('contact_phone')->nullable();
    $table->string('contact_address')->nullable();
    $table->string('contact_address_en')->nullable();
    $table->string('contact_map_url')->nullable();   // رابط خريطة
    // Newsletter
    $table->boolean('newsletter_enabled')->default(true);
    $table->string('newsletter_title')->nullable();
    $table->string('newsletter_title_en')->nullable();
    $table->string('newsletter_placeholder')->nullable();
    $table->string('newsletter_placeholder_en')->nullable();
    $table->string('newsletter_button_text')->nullable();
    $table->string('newsletter_button_text_en')->nullable();
    // Copyright
    $table->text('copyright_text')->nullable();
    $table->text('copyright_text_en')->nullable();
    // Back to top
    $table->boolean('back_to_top_enabled')->default(true);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_settings');
    }
};
