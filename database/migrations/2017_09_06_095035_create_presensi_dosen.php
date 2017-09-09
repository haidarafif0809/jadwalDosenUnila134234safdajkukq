<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePresensiDosen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        Schema::create('presensi', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_user');
            $table->integer('id_ruangan')->nullable();
            $table->integer('id_jadwal');
            $table->string('longitude');
            $table->string('latitude');
            $table->string('foto');            
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
        Schema::dropIfExists('presensi');
    }
}
