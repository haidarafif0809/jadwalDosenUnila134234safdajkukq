<?php

use Illuminate\Database\Seeder;
use App\Angkatan;

class AngkatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Angkatan::create(['kode_angkatan' => 1,'nama_angkatan' => 'Angkatan 2013']);
        Angkatan::create(['kode_angkatan' => 2,'nama_angkatan' => 'Angkatan 2014']);
        Angkatan::create(['kode_angkatan' => 3,'nama_angkatan' => 'Angkatan 2015']);
        Angkatan::create(['kode_angkatan' => 4,'nama_angkatan' => 'Angkatan 2016']);
        Angkatan::create(['kode_angkatan' => 5,'nama_angkatan' => 'Angkatan 2017']);
    }
}
