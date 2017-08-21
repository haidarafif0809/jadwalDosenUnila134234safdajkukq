<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Master_block extends Model
{
    protected $fillable = ['id','kode_block','nama_block','id_angkatan'];

    		public function angkatan()
		  {
			return $this->hasOne('App\Angkatan','id','id_angkatan');
		  }
		  
}
