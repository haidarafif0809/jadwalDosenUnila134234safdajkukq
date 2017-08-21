<?php

use Illuminate\Database\Seeder;
use App\UserPjDosen;

class UserPjDosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
	    $user_pj_dosen = new UserPjDosen();
	    $user_pj_dosen->id_master_block = "1";
	    $user_pj_dosen->id_pj_dosen = "2"; 
	    $user_pj_dosen->save(); 

	    $user_pj_dosen = new UserPjDosen();
	    $user_pj_dosen->id_master_block = "2";
	    $user_pj_dosen->id_pj_dosen = "3"; 
	    $user_pj_dosen->save(); 

	    $user_pj_dosen = new UserPjDosen();
	    $user_pj_dosen->id_master_block = "3";
	    $user_pj_dosen->id_pj_dosen = "4"; 
	    $user_pj_dosen->save();
    }
}
