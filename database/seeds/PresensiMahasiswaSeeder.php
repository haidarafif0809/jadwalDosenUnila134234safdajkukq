<?php

use Illuminate\Database\Seeder;
use App\PresensiMahasiswa;

class PresensiMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
    $presensi_mahasiswa = new PresensiMahasiswa();
    $presensi_mahasiswa->id_user   = "1";    
    $presensi_mahasiswa->id_ruangan   = "1";  
    $presensi_mahasiswa->id_jadwal   = "1";  
    $presensi_mahasiswa->longitude   = "00000";    
    $presensi_mahasiswa->latitude   = "00000";   
    $presensi_mahasiswa->foto   = "#";  
    $presensi_mahasiswa->jarak_ke_lokasi_absen   = "111111"; 
    $presensi_mahasiswa->id_block   = "1";   
    $presensi_mahasiswa->save();

    $presensi_mahasiswa = new PresensiMahasiswa();
    $presensi_mahasiswa->id_user   = "2";    
    $presensi_mahasiswa->id_ruangan   = "1";  
    $presensi_mahasiswa->id_jadwal   = "1";  
    $presensi_mahasiswa->longitude   = "00000";    
    $presensi_mahasiswa->latitude   = "00000";   
    $presensi_mahasiswa->foto   = "#";  
    $presensi_mahasiswa->jarak_ke_lokasi_absen   = "222222";
    $presensi_mahasiswa->id_block   = "1";    
    $presensi_mahasiswa->save();
    }
}
