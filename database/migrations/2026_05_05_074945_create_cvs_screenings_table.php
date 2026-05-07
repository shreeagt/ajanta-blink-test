<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCvsScreeningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cvs_screenings', function (Blueprint $table) {
            $table->id();
            $table->string('emp_code');
            $table->unsignedBigInteger('blink_test_id')->nullable();
            $table->json('symptom_data');
            $table->integer('total_score');
            $table->boolean('has_cvs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cvs_screenings');
    }
}
