<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TambahIdBlockIdRuanganIdMataKuliahKeJadwalDosen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jadwal_dosens', function (Blueprint $table) {
            //

            $table->integer('id_block')->nullable();
            $table->integer('id_mata_kuliah')->nullable();
            $table->integer('id_ruangan')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jadwal_dosens', function (Blueprint $table) {
            //
             $table->dropColumn('id_block');
             $table->dropColumn('id_mata_kuliah');
             $table->dropColumn('id_ruangan');
        });
    }
}
