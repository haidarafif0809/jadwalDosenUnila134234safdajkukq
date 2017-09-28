<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
	protected $table = 'presensi';
   	protected $fillable = ['id_user', 'id_ruangan', 'id_jadwal', 'longitude' , 'latitude' , 'foto', 'jarak_ke_lokasi_absen','id_block'];


   	public function dosen()
	{
		  	return $this->hasOne('App\User','id','id_user');
	}

	public function penjadwalan()
	{
		  	return $this->hasOne('App\Penjadwalan','id','id_jadwal');
	}

	public function ruangan()
	{
		  	return $this->hasOne('App\Master_ruangan','id','id_ruangan');
	}
}
