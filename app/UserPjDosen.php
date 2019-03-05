<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPjDosen extends Model
{
    //
    	protected $fillable = ['id_master_block','id_pj_dosen'];
		  
    	public function dosen()
		  {
		  	return $this->hasOne('App\User','id','id_pj_dosen');
		  }

	        public function block()
	      {
	        return $this->hasOne('App\Master_block','id','id_master_block');
	      }
}
