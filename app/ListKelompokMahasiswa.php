<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ListKelompokMahasiswa extends Model
{
    //

     protected $fillable = ['id_mahasiswa','id_kelompok_mahasiswa'];

		public function mahasiswa()
	  {
		return $this->hasOne('App\User','id','id_mahasiswa');
	  }	
	  
	  public function kelompok()
	  {
		return $this->hasOne('App\KelompokMahasiswa','id','id_kelompok_mahasiswa');
	  }
}
