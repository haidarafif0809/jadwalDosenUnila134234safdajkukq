<?php

use Illuminate\Database\Seeder;
use App\Modul;

class ModulSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $modul = Modul::create(['kode_modul' => '01','nama_modul'=> 'modul 1 ,minggu 1 ']);
        $modul = Modul::create(['kode_modul' => '02','nama_modul'=> 'modul 2 ,minggu 2 ']);
        $modul = Modul::create(['kode_modul' => '03','nama_modul'=> 'modul 3 ,minggu 3 ']);
        $modul = Modul::create(['kode_modul' => '04','nama_modul'=> 'modul 4 ,minggu 4 ']);
    }
}
