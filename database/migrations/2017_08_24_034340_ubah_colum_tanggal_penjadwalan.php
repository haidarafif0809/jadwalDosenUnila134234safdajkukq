<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UbahColumTanggalPenjadwalan extends Migration
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
            $table->date('tanggal')->nullable()->change(); 

        });
        Schema::table('jadwal_dosens', function (Blueprint $table) {
            // 
            $table->date('tanggal')->nullable()->change();

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
        Schema::table('penjadwalans', function (Blueprint $table) {
            // 
            $table->date('tanggal')->nullable()->change(); 
        });
        Schema::table('jadwal_dosens', function (Blueprint $table) {
            // 
            $table->date('tanggal')->nullable()->change(); 
        });
    }
}
