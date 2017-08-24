<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TambahColumLongitudeRuangan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('master_ruangans', function (Blueprint $table) {
            //
        $table->string('longitude')->nullable();
        $table->string('batas_jarak_absen')->nullable();
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
        Schema::table('master_ruangans', function (Blueprint $table) {
            //
            $table->dropColumn('longitude')->nullable();
            $table->dropColumn('batas_jarak_absen')->nullable();
        });
    }
}
