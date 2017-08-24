<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TambahColumLattitudeRuangan extends Migration
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
        $table->string('latitude')->nullable();         });
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
            $table->dropColumn('latitude')->nullable(); 
        });
    }
}
