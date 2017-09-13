<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingSlidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_slides', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slide_1');
            $table->string('slide_2');
            $table->string('slide_3'); 
            $table->string('judul_slide_1');
            $table->string('judul_slide_2');
            $table->string('judul_slide_3'); 
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
        Schema::dropIfExists('setting_slides');
    }
}
