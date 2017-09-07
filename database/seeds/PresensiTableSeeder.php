<?php

use Illuminate\Database\Seeder;

class PresensiTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
                //
    $presensi = new Presensi();
    $presensi->id_user   = "6";
    $presensi->id_ruangan   = "2";
    $presensi->id_jadwal   = "1";
    $presensi->longitude   = "105.2162817";
    $presensi->latitude   = "-5.3929288";
    $presensi->foto   = "andaglos.jpg";
    $presensi->save();

    }
}
