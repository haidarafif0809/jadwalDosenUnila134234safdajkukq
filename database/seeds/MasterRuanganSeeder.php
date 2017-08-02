<?php

use Illuminate\Database\Seeder;
use App\Master_ruangan;

class MasterRuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

    // Membuat Sample Ruangan
    $master_ruangan = new Master_ruangan();
    $master_ruangan->kode_ruangan   = "01";
    $master_ruangan->nama_ruangan   = "Programmer";
    $master_ruangan->lokasi_ruangan = "Gedung A";
    $master_ruangan->save();

    // Membuat Sample Ruangan
    $master_ruangan = new Master_ruangan();
    $master_ruangan->kode_ruangan   = "02";
    $master_ruangan->nama_ruangan   = "Tester";
    $master_ruangan->lokasi_ruangan = "Gedung B";
    $master_ruangan->save();

    // Membuat Sample Ruangan
    $master_ruangan = new Master_ruangan();
    $master_ruangan->kode_ruangan   = "03";
    $master_ruangan->nama_ruangan   = "Implementator";
    $master_ruangan->lokasi_ruangan = "Gedung C";
    $master_ruangan->save();

    }
}
