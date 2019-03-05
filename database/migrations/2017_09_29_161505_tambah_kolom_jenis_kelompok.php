<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TambahKolomJenisKelompok extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
          Schema::table('kelompok_mahasiswas', function (Blueprint $table) {
            $table->string('jenis_kelompok')->nullable();
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
          Schema::table('kelompok_mahasiswas', function (Blueprint $table) {
            //
            $table->dropColumn("jenis_kelompok");
        }); 
    }
}
