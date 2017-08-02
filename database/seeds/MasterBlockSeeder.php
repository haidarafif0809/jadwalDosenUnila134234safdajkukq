<?php

use Illuminate\Database\Seeder;
use App\Master_block;

class MasterBlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    // Membuat Sample Block
	    $master_block = new Master_block();
	    $master_block->kode_block   = "B001";
	    $master_block->nama_block   = "Blok A";
	    $master_block->save();

    // Membuat Sample Block
	    $master_block = new Master_block();
	    $master_block->kode_block   = "B002";
	    $master_block->nama_block   = "Blok B";
	    $master_block->save();

    // Membuat Sample Block
	    $master_block = new Master_block();
	    $master_block->kode_block   = "B003";
	    $master_block->nama_block   = "Blok C";
	    $master_block->save();
    }
}
