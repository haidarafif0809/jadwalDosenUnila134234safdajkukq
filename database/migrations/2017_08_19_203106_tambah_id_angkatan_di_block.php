<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TambahIdAngkatanDiBlock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_blocks', function (Blueprint $table) {
            //
            $table->integer('id_angkatan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_blocks', function (Blueprint $table) {
           $table->dropColumn('id_angkatan');
        });
    }
}
