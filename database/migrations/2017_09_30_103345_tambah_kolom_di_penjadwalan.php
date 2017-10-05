<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TambahKolomDiPenjadwalan extends Migration
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
            $table->integer('id_materi')->nullable();
            $table->integer('id_kelompok')->nullable();
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
            $table->dropColumn("id_materi");
            $table->dropColumn("id_kelompok");
        }); 
    }
}
