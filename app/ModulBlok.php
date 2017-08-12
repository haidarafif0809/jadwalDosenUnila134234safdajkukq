<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModulBlok extends Model
{
    //

         protected $table = 'modul_bloks';
     protected $fillable = ['id_modul_blok','id_modul','id_blok','dari_tanggal','sampai_tanggal','urutan'];
}
