<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_translations', function (Blueprint $table) {
            $table->id();
            $table->string('lang_code', 5);      // 'en', 'hi', 'gu', etc.
            $table->string('page', 50);           // 'guide', 'blink', 'blink_result', 'cvs', 'cvs_result', 'thank_you'
            $table->string('key', 100);           // 'title', 'never', 'start_btn', etc.
            $table->text('value');                // Translated string
            $table->timestamps();

            $table->unique(['lang_code', 'page', 'key'], 'unique_translation');
            $table->index('lang_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_translations');
    }
};
