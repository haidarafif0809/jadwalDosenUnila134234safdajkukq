<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJadwalRuangansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jadwal_ruangans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_jadwal');
            $table->integer('id_ruangan');
            $table->string('tanggal');
            $table->string('waktu_mulai');
            $table->string('waktu_selesai');
            $table->integer('status_jadwal')->default(0);
            $table->string('tipe_jadwal')->nullable();
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
        Schema::dropIfExists('jadwal_ruangans');
    }
}
