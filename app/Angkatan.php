<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Angkatan extends Model
{
    //

     protected $table = 'angkatan';
     protected $fillable = ['kode_angkatan','nama_angkatan'];
}
