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
    $penjadwalans->id_jadwal_dosen = "1";
    $penjadwalans->tanggal = "2017-07-07";
    $penjadwalans->waktu_mulai = "01:00";
    $penjadwalans->waktu_selesai = "02:00";
    $penjadwalans->save();
    }
}
