<?php

use Illuminate\Database\Seeder;
use App\Penjadwalan;

class PenjadwalanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
    $penjadwalans = new Penjadwalan();
    $penjadwalans->id_block   = "1";
    $penjadwalans->id_mata_kuliah   = "1";
    $penjadwalans->id_ruangan = "1"; 
    $penjadwalans->tanggal = "2017-08-18";
    $penjadwalans->waktu_mulai = "07:00";
    $penjadwalans->waktu_selesai = "08:00";
    $penjadwalans->id_modul   = "1";
    $penjadwalans->save();
    }
}
