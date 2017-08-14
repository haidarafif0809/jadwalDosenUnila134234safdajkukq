<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TambahColumJadwalDosen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('jadwal_dosens', function (Blueprint $table) {
            //
        $table->string('tanggal')->nullable();
        $table->string('waktu_mulai')->nullable();
        $table->string('waktu_selesai')->nullable();
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
        Schema::table('jadwal_dosens', function (Blueprint $table) {
            //
            $table->dropColumn('tanggal');
            $table->dropColumn('waktu_mulai');
            $table->dropColumn('waktu_selesai');
        });
    }
}
