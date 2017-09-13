<?php

use Illuminate\Database\Seeder;
use App\SettingSlide;

class SettingSlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
    $settingslide = new SettingSlide();
    $settingslide->slide   = "#";   
    $settingslide->judul_slide  = "-";  
    $settingslide->save();
    }
}
