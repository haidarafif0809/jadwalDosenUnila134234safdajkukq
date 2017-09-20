<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Master_block extends Model
{
    protected $fillable = ['id','kode_block','nama_block','id_angkatan','id_user_pj_dosen'];

    		public function angkatan()
		  {
			return $this->hasOne('App\Angkatan','id','id_angkatan');
		  }
		  
 		   public function user_pj_dosen()
          {
            return $this->hasOne('App\UserPjDosen','id_master_block','id');
          }  

    public function getJumlahJadwalAttribute() {

        $jumlah_jadwal = Penjadwalan::where('id_block',$this->id)->where('status_jadwal',1)->count();

        return $jumlah_jadwal;
        
    }
}
