<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KelompokMahasiswa extends Model
{
    //
        protected $fillable = ['nama_kelompok_mahasiswa','id_angkatan','jenis_kelompok'];

        	public function angkatan()
		  {
			return $this->hasOne('App\Angkatan','id','id_angkatan');
		  }
}
