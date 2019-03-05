<?php

use Illuminate\Database\Seeder;
use App\SettingWaktu; //Modal  

class SettingWaktuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
    $settingwaktu = new SettingWaktu();
    $settingwaktu->waktu_mulai   = "07:00";
    $settingwaktu->waktu_selesai   = "08:00"; 
    $settingwaktu->save();

    $settingwaktu = new SettingWaktu();
    $settingwaktu->waktu_mulai   = "08:00";
    $settingwaktu->waktu_selesai   = "09:00"; 
    $settingwaktu->save();

    $settingwaktu = new SettingWaktu();
    $settingwaktu->waktu_mulai   = "10:00";
    $settingwaktu->waktu_selesai   = "11:00"; 
    $settingwaktu->save();

    $settingwaktu = new SettingWaktu();
    $settingwaktu->waktu_mulai   = "11:00";
    $settingwaktu->waktu_selesai   = "12:00"; 
    $settingwaktu->save();
    }
}
