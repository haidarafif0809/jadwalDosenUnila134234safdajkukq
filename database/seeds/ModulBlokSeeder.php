<?php

use Illuminate\Database\Seeder;
use App\ModulBlok;

class ModulBlokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        ModulBlok::create(['id_modul' => 1 ,'id_blok' => 1,'dari_tanggal' => '2017-08-18','sampai_tanggal' => '2017-08-22']);
        ModulBlok::create(['id_modul' => 2 ,'id_blok' => 1,'dari_tanggal' => '2017-08-25','sampai_tanggal' => '2017-08-19']);
    }
}
