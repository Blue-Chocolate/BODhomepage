<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impact_beneficiary_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('impact_report_id')->constrained()->cascadeOnDelete();

            $table->string('period');               // الفترة الزمنية e.g. "Q1 2025"
            $table->unsignedInteger('beneficiaries'); // عدد المستفيدين في هذه الفترة

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impact_beneficiary_records');
    }
};