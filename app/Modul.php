<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modul extends Model
{
    //
     protected $table = 'moduls';
     protected $fillable = ['id','kode_modul','nama_modul'];
}
