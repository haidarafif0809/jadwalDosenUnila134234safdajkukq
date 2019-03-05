<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TambahKolomIdKelompokPresensiMahasiswa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('presensi_mahasiswas', function (Blueprint $table) {
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
        Schema::table('presensi_mahasiswas', function (Blueprint $table) {
            $table->dropColumn("id_kelompok");
        }); 
    }
}
