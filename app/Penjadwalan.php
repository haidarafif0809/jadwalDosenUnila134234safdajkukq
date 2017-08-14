<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penjadwalan extends Model
{
    //
     protected $fillable = ['id_block','id_mata_kuliah','id_ruangan','tanggal','waktu_mulai','waktu_selesai','status_jadwal'];

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


		public function scopeStatusRuangan($query, $request)
		{

			$query->where('id_ruangan',$request->id_ruangan)->where('tanggal',$request->tanggal)->where(function ($query) use ($request) {
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
                            })->where('status_jadwal','<','2');

              return $query;
		
		}

}
