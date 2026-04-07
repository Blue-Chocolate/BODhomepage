<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Hero Slides ──────────────────────────────────────────────────────
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();

            $table->enum('site', ['waleda', 'manzuma', 'both'])->default('waleda')->index();

            // Bilingual content
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('subtitle_ar')->nullable();
            $table->text('subtitle_en')->nullable();

            // Background media
            $table->string('background_image')->nullable();        // stored path (disk: public)
            $table->string('background_image_mobile')->nullable(); // portrait crop for mobile
            $table->string('background_video_url')->nullable();    // YouTube / Vimeo / direct

            // Overlay
            $table->string('overlay_color', 7)->default('#000000');        // hex
            $table->unsignedTinyInteger('overlay_opacity')->default(40);   // 0–100

            // Transition & timing
            $table->enum('transition_effect', ['fade', 'slide', 'zoom', 'ken_burns'])->default('fade');
            $table->unsignedSmallInteger('display_duration')->default(5);  // seconds

            // CTA – Primary
            $table->string('cta_primary_text_ar')->nullable();
            $table->string('cta_primary_text_en')->nullable();
            $table->string('cta_primary_url')->nullable();
            $table->enum('cta_primary_style', ['solid', 'outline', 'ghost'])->default('solid');

            // CTA – Secondary
            $table->string('cta_secondary_text_ar')->nullable();
            $table->string('cta_secondary_text_en')->nullable();
            $table->string('cta_secondary_url')->nullable();
            $table->boolean('cta_secondary_visible')->default(true);

            // Slider global controls (per slide, inherited by JS if only 1 slide)
            $table->boolean('autoplay')->default(true);
            $table->boolean('loop')->default(true);
            $table->boolean('show_arrows')->default(true);
            $table->boolean('show_dots')->default(true);

            // Ordering & visibility
            $table->unsignedSmallInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });

        // ─── Statistics Bar ───────────────────────────────────────────────────
        Schema::create('hero_statistics', function (Blueprint $table) {
            $table->id();
            $table->enum('site', ['waleda', 'manzuma', 'both'])->default('waleda')->index();
            $table->string('icon')->nullable();     // Heroicon name e.g. "user-group"
            $table->string('value');                // e.g. "150+" or "10,000"
            $table->string('label_ar');
            $table->string('label_en');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_statistics');
        Schema::dropIfExists('hero_slides');
    }
};