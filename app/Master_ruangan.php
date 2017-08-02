<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Master_ruangan extends Model
{
    //
    protected $fillable = ['id','kode_ruangan','nama_ruangan','lokasi_ruangan'];
}
