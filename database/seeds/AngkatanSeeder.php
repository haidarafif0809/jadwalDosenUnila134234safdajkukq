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
        Angkatan::create(['kode_angkatan' => 1,'nama_angkatan' => 'angkatan 1']);
        Angkatan::create(['kode_angkatan' => 2,'nama_angkatan' => 'angkatan 2']);
        Angkatan::create(['kode_angkatan' => 3,'nama_angkatan' => 'angkatan 3']);
        Angkatan::create(['kode_angkatan' => 4,'nama_angkatan' => 'angkatan 4']);
    }
}
