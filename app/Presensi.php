<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
	protected $table = 'presensi';
   	protected $fillable = ['id_user', 'id_ruangan', 'id_jadwal', 'longitude' , 'latitude' , 'foto', 'jarak_ke_lokasi_absen'];
}
