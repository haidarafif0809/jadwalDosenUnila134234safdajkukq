<?php

use Illuminate\Database\Seeder;
use App\Master_mata_kuliah;

class MasterMataKuliahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    
    // Membuat Sample Ruangan
    $master_mata_kuliah = new master_mata_kuliah();
    $master_mata_kuliah->kode_mata_kuliah   = "01";
    $master_mata_kuliah->nama_mata_kuliah   = "Ke Dokteran"; 
    $master_mata_kuliah->save();

    // Membuat Sample Ruangan
    $master_mata_kuliah = new master_mata_kuliah();
    $master_mata_kuliah->kode_mata_kuliah   = "02";
    $master_mata_kuliah->nama_mata_kuliah   = "Programmer"; 
    $master_mata_kuliah->save();

    // Membuat Sample Ruangan
    $master_mata_kuliah = new master_mata_kuliah();
    $master_mata_kuliah->kode_mata_kuliah   = "03";
    $master_mata_kuliah->nama_mata_kuliah   = "Akuntasi"; 
    $master_mata_kuliah->save();
    }
}
