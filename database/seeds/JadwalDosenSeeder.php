<?php

use Illuminate\Database\Seeder;
use App\Jadwal_dosen;


class JadwalDosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 
	    $jadwal_dosen = new Jadwal_dosen();
	    $jadwal_dosen->id_jadwal = "1";
	    $jadwal_dosen->id_dosen = "1";
	    $jadwal_dosen->save();

        $jadwal_dosen = new Jadwal_dosen();
        $jadwal_dosen->id_jadwal = "1";
        $jadwal_dosen->id_dosen = "2";
        $jadwal_dosen->save();
    }
}
