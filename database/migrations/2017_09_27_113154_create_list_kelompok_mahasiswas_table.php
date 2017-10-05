<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListKelompokMahasiswasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_kelompok_mahasiswas', function (Blueprint $table) {
            $table->increments('id');
             $table->integer('id_mahasiswa')->nullable();
             $table->integer('id_kelompok_mahasiswa')->nullable();
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
        Schema::dropIfExists('list_kelompok_mahasiswas');
    }
}
