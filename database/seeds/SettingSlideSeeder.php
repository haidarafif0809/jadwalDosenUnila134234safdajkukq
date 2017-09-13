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
    $settingslide->slide_1   = "#"; 
    $settingslide->slide_2   = "#"; 
    $settingslide->slide_3   = "#"; 
    $settingslide->judul_slide_1   = "-"; 
    $settingslide->judul_slide_2   = "-"; 
    $settingslide->judul_slide_3   = "-"; 
    $settingslide->save();
    }
}
