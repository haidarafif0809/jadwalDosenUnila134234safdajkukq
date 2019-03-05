<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulBlokTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::create('modul_bloks', function (Blueprint $table) {
            //

            $table->increments('id_modul_blok');
            $table->integer('id_modul');
            $table->integer('id_blok');
            $table->date('dari_tanggal')->nullable();
            $table->date('sampai_tanggal')->nullable();
            $table->integer('urutan')->default(0);
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
        //

         Schema::dropIfExists('modul_bloks');
    }
}
