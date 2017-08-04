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
    }
}
