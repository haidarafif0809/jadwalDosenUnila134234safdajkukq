<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(MasterRuanganSeeder::class);
        $this->call(MasterMataKuliahSeeder::class);
        $this->call(MasterBlockSeeder::class);
        $this->call(PenjadwalanSeeder::class);
        $this->call(JadwalDosenSeeder::class);
        $this->call(ModulSeeder::class);
        $this->call(ModulBlokSeeder::class);
        $this->call(SettingWaktuSeeder::class);
        $this->call(AngkatanSeeder::class);
        $this->call(UserPjDosenSeeder::class);
        $this->call(SettingSlideSeeder::class);
        $this->call(PresensiMahasiswaSeeder::class);
        $this->call(PresensiDosenSeeder::class);
    }
}
