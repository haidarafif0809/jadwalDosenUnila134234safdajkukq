<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Yajra\Auditable\AuditableTrait;

class Penjadwalan extends Model
{
     use AuditableTrait;
     protected $fillable = ['id_block','id_mata_kuliah','id_ruangan','tanggal','waktu_mulai','waktu_selesai','status_jadwal','id_modul','tipe_jadwal','id_materi','id_kelompok','created_by','updated_by'];

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

      public function modul()
      {
        return $this->hasOne('App\ModulBlok','id_modul_blok','id_modul');
      } 

      public function materi()
      {
        return $this->hasOne('App\Materi','id','id_materi');
      } 

      public function kelompok()
      {
        return $this->hasOne('App\KelompokMahasiswa','id','id_kelompok');
      } 

    //MENGECEK TAMBAH PENDAJWALAN, RUANGAN YANG SAMA DAN DI JADWAL YANG BERSMAAN
    public function scopeStatusRuangan($query, $request,$data_setting_waktu)
    {

      $query->where('id_ruangan',$request->id_ruangan)->where('tanggal',$request->tanggal)->where(function ($query) use ($request,$data_setting_waktu) {
                  $query->where('waktu_mulai',$data_setting_waktu[0])->orwhere( function ($query) use ($request,$data_setting_waktu) {
                    $query->where(function ($query) use ($request,$data_setting_waktu) {
                      $query->where('waktu_mulai','<',$data_setting_waktu[0])->where('waktu_selesai','>',$data_setting_waktu[0]);
                        })->orwhere(function($query) use ($request,$data_setting_waktu) {
                         $query->where('waktu_mulai','<',$data_setting_waktu[0])->where('waktu_selesai','>',$data_setting_waktu[0]);
                          });
                        });
                      })->where(function($query) use ($request,$data_setting_waktu) {
                $query->where('waktu_selesai',$data_setting_waktu[1])
                      ->orwhere(function($query) use ($request,$data_setting_waktu){
                        $query->where('waktu_selesai','>=',$data_setting_waktu[1])->where('waktu_mulai','<=',$data_setting_waktu[1]);
                        })->orwhere(function($query) use ($request,$data_setting_waktu) {
                           $query->where('waktu_selesai','<',$data_setting_waktu[1])->where('waktu_mulai','<=',$data_setting_waktu[1]);
                          });
                            })->where('status_jadwal','<','2');

              return $query;
    
    }

    //MENGECEK UPDATE PENDAJWALAN, RUANGAN YANG SAMA DAN DI JADWAL YANG BERSMAAN
		public function scopeStatusRuanganEdit($query, $request,$data_setting_waktu,$id)
		{

			$query->where('id','!=',$id)->where('id_ruangan',$request->id_ruangan)->where('tanggal',$request->tanggal)->where(function ($query) use ($request,$data_setting_waktu) {
                  $query->where('waktu_mulai',$data_setting_waktu[0])->orwhere( function ($query) use ($request,$data_setting_waktu) {
                    $query->where(function ($query) use ($request,$data_setting_waktu) {
                      $query->where('waktu_mulai','<',$data_setting_waktu[0])->where('waktu_selesai','>',$data_setting_waktu[0]);
                        })->orwhere(function($query) use ($request,$data_setting_waktu) {
                         $query->where('waktu_mulai','<',$data_setting_waktu[0])->where('waktu_selesai','>',$data_setting_waktu[0]);
                          });
                        });
                      })->where(function($query) use ($request,$data_setting_waktu) {
                $query->where('waktu_selesai',$data_setting_waktu[1])
                      ->orwhere(function($query) use ($request,$data_setting_waktu){
                        $query->where('waktu_selesai','>=',$data_setting_waktu[1])->where('waktu_mulai','<=',$data_setting_waktu[1]);
                        })->orwhere(function($query) use ($request,$data_setting_waktu) {
                           $query->where('waktu_selesai','<',$data_setting_waktu[1])->where('waktu_mulai','<=',$data_setting_waktu[1]);
                          });
                            })->where('status_jadwal','<','2');

              return $query;
		
		}

}
