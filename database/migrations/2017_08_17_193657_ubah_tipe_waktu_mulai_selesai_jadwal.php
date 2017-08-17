<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UbahTipeWaktuMulaiSelesaiJadwal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('penjadwalans', function (Blueprint $table) {
            //


            $table->time('waktu_mulai')->nullable()->change();
            $table->time('waktu_selesai')->nullable()->change();

        });
        Schema::table('jadwal_dosens', function (Blueprint $table) {
            //


            $table->time('waktu_mulai')->nullable()->change();
            $table->time('waktu_selesai')->nullable()->change();

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
         //
        Schema::table('penjadwalans', function (Blueprint $table) {
            //

            $table->time('waktu_mulai')->nullable()->change();
            $table->time('waktu_selesai')->nullable()->change();
        });
        Schema::table('jadwal_dosens', function (Blueprint $table) {
            //

            $table->time('waktu_mulai')->nullable()->change();
            $table->time('waktu_selesai')->nullable()->change();
        });

    }
}
