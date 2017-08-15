<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MahasiswaBlock extends Model
{
    //
     protected $table = 'mahasiswa_block';
     protected $fillable = ['id_mahasiswa','id_block'];

		public function mahasiswa()
	  {
		return $this->hasOne('App\User','id','id_mahasiswa');
	  }	
	  
	  public function block()
	  {
		return $this->hasOne('App\Master_block','id','id_block');
	  }
}
