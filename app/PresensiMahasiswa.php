<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PresensiMahasiswa extends Model
{
   	protected $fillable = ['id_user', 'id_ruangan', 'id_jadwal', 'longitude' , 'latitude' , 'foto', 'jarak_ke_lokasi_absen', 'id_block', 'id_kelompok'];
}
