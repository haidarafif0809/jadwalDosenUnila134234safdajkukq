<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jadwal_dosen extends Model
{
    // 
    	protected $fillable = ['id_jadwal','id_dosen'];

		public function jadwal()
		  {
			return $this->hasOne('App\Penjadwalan','id','id_jadwal');
		  }
		  
    	public function dosen()
		  {
		  	return $this->hasOne('App\User','id','id_dosen');
		  }
		
}
