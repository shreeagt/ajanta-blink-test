<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cvs_symptoms', function (Blueprint $table) {
            $table->id();
            $table->string('symptom_key', 50);   // 'burning', 'itching', 'foreign_body', etc.
            $table->unsignedTinyInteger('sort_order');  // 1-16
            $table->string('lang_code', 5);       // 'en', 'hi', 'gu', etc.
            $table->text('symptom_text');          // Translated symptom name from CVS-Q document
            $table->timestamps();

            $table->unique(['symptom_key', 'lang_code'], 'unique_symptom');
            $table->index('lang_code');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cvs_symptoms');
    }
};
