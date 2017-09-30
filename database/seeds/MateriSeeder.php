<?php

use Illuminate\Database\Seeder;
use App\Materi;

class MateriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

	   	// Membuat Sample Materi 1
	    $materi = new Materi();
	    $materi->nama_materi   = "Materi A";
	    $materi->save();

	    // Membuat Sample Materi 2
	    $materi = new Materi();
	    $materi->nama_materi   = "Materi B";
	    $materi->save();

	    // Membuat Sample Materi 3
	    $materi = new Materi();
	    $materi->nama_materi   = "Materi C";
	    $materi->save();

    }
}
