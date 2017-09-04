<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TambahKolomStatusJadwalDiJadwalDosen extends Migration
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
            $table->integer("status_jadwal")->default(0);
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
            $table->dropColumn("status_jadwal");
        });
    }
}
