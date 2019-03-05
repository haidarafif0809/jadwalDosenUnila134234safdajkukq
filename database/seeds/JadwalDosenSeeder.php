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
	    $jadwal_dosen->id_dosen = "5";
        $jadwal_dosen->tanggal = "2017-08-11";
        $jadwal_dosen->waktu_mulai = "01:00";
        $jadwal_dosen->waktu_selesai = "02:00";
        $jadwal_dosen->id_block = "1";
        $jadwal_dosen->id_mata_kuliah = "1";
        $jadwal_dosen->id_ruangan = "1"; 
	    $jadwal_dosen->save();

        $jadwal_dosen = new Jadwal_dosen();
        $jadwal_dosen->id_jadwal = "1";
        $jadwal_dosen->id_dosen = "6";
        $jadwal_dosen->tanggal = "2017-08-11";
        $jadwal_dosen->waktu_mulai = "01:00";
        $jadwal_dosen->waktu_selesai = "02:00";
         $jadwal_dosen->id_block = "1";
        $jadwal_dosen->id_mata_kuliah = "1";
        $jadwal_dosen->id_ruangan = "1";
        $jadwal_dosen->save();


        $jadwal_dosen = new Jadwal_dosen();
        $jadwal_dosen->id_jadwal = "1";
        $jadwal_dosen->id_dosen = "7";
        $jadwal_dosen->tanggal = "2017-08-11";
        $jadwal_dosen->waktu_mulai = "01:00";
        $jadwal_dosen->waktu_selesai = "02:00";
         $jadwal_dosen->id_block = "1";
        $jadwal_dosen->id_mata_kuliah = "1";
        $jadwal_dosen->id_ruangan = "1";
        $jadwal_dosen->save();
    }
}
