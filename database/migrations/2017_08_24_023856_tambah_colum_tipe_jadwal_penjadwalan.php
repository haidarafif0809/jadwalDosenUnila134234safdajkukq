<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TambahColumTipeJadwalPenjadwalan extends Migration
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
        $table->string('tipe_jadwal'); 
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
            $table->dropColumn('tipe_jadwal'); 
        });
    }
}
