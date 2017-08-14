<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jadwal_dosen extends Model
{
    // 
    	protected $fillable = ['id_jadwal','id_dosen','tanggal','waktu_mulai','waktu_selesai','id_block','id_mata_kuliah','id_ruangan'];

		public function jadwal()
		  {
			return $this->hasOne('App\Penjadwalan','id','id_jadwal');
		  }
		  
    	public function dosen()
		  {
		  	return $this->hasOne('App\User','id','id_dosen');
		  }

        public function block()
      {
        return $this->hasOne('App\Master_block','id','id_block');
      }

      public function mata_kuliah()
      {
        return $this->hasOne('App\Master_mata_kuliah','id','id_mata_kuliah');
      }

      public function ruangan()
      {
        return $this->hasOne('App\Master_ruangan','id','id_ruangan');
      } 



		public function scopeStatusDosen($query, $request,$user_dosen)
		{

			$query->where('id_dosen',$user_dosen)->where('tanggal',$request->tanggal)->where(function ($query) use ($request) {
                  $query->where('waktu_mulai',$request->waktu_mulai)->orwhere( function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                      $query->where('waktu_mulai','<',$request->waktu_mulai)->where('waktu_selesai','>',$request->waktu_mulai);
                        })->orwhere(function($query) use ($request) {
                         $query->where('waktu_mulai','<',$request->waktu_mulai)->where('waktu_selesai','>',$request->waktu_mulai);
                          });
                        });
                      })->where(function($query) use ($request) {
                $query->where('waktu_selesai',$request->waktu_selesai)
                      ->orwhere(function($query) use ($request){
                        $query->where('waktu_selesai','>=',$request->waktu_selesai)->where('waktu_mulai','<=',$request->waktu_selesai);
                        })->orwhere(function($query) use ($request) {
                           $query->where('waktu_selesai','<',$request->waktu_selesai)->where('waktu_mulai','<=',$request->waktu_selesai);
                          });
                            });

              return $query;
		
		}		
}
