<?php

use Illuminate\Database\Seeder;
use App\Presensi;

class PresensiDosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
    $presensi_dosen = new Presensi();
    $presensi_dosen->id_user   = "5";    
    $presensi_dosen->id_ruangan   = "1";  
    $presensi_dosen->id_jadwal   = "1";  
    $presensi_dosen->longitude   = "00000";    
    $presensi_dosen->latitude   = "00000";   
    $presensi_dosen->foto   = "#";  
    $presensi_dosen->jarak_ke_lokasi_absen   = "111111";   
    $presensi_dosen->id_block   = "1";  
    $presensi_dosen->save();

    $presensi_dosen = new Presensi();
    $presensi_dosen->id_user   = "6";    
    $presensi_dosen->id_ruangan   = "1";  
    $presensi_dosen->id_jadwal   = "1";  
    $presensi_dosen->longitude   = "00000";    
    $presensi_dosen->latitude   = "00000";   
    $presensi_dosen->foto   = "#";  
    $presensi_dosen->jarak_ke_lokasi_absen   = "111111";  
    $presensi_dosen->id_block   = "1";  
    $presensi_dosen->save();
    }
}
