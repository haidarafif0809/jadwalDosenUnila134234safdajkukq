<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


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

      public function presensi()
      {
        return $this->hasOne('App\Presensi','id_jadwal','id_jadwal');
      } 



    //MENGECEK DOSEN KETIKA TAMBAH DI PENJADWALAN
    public function scopeStatusDosen($query, $request,$user_dosen,$data_setting_waktu)
    {

      $query->where('id_dosen',$user_dosen)->where('tanggal',$request->tanggal)->where(function ($query) use ($request,$data_setting_waktu) {
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

    
    //MENGECEK DOSEN KETIKA TAMBAH DI PENJADWALAN 
		public function scopeStatusDosenEdit($query, $request,$user_dosen,$data_setting_waktu,$id)
		{

			$query->where('id_jadwal','!=',$id)->where('id_dosen',$user_dosen)->where('tanggal',$request->tanggal)->where(function ($query) use ($request,$data_setting_waktu) {
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

// SCOE LIST JADWAL DOSEN => ANDROID 
    public function scopeListJadwalDosen($query_list_jadwal_dosen,$id_dosen){

      $waktu = date("Y-m-d H:i:s");// waktu sekarang

      $query_list_jadwal_dosen->select('penjadwalans.id_materi AS id_materi','penjadwalans.tipe_jadwal AS tipe_jadwal','jadwal_dosens.id_mata_kuliah AS id_mata_kuliah','jadwal_dosens.id_jadwal AS id_jadwal','jadwal_dosens.id_ruangan AS id_ruangan','jadwal_dosens.tanggal AS tanggal', 'jadwal_dosens.waktu_mulai AS waktu_mulai', 'jadwal_dosens.waktu_selesai AS waktu_selesai','master_mata_kuliahs.nama_mata_kuliah','master_ruangans.nama_ruangan AS ruangan','master_ruangans.longitude AS longitude','master_ruangans.latitude AS latitude','master_ruangans.batas_jarak_absen AS batas_jarak_absen','penjadwalans.tipe_jadwal AS tipe_jadwal')// DATA YANG DIAMBIL TANGGAL,WAKTU MULAI, WAKTU SELESAI, NAMA MATA KULIAH,  RUANGAN, LATITUDE , LONGITUDE, BATAS JARAK ABSEN , TIPE JADWAL

                        ->leftJoin('master_mata_kuliahs','jadwal_dosens.id_mata_kuliah','=','master_mata_kuliahs.id')
                        //LEFT JOIN KE TABLE MATA KULIAH
                        ->leftJoin('master_ruangans','jadwal_dosens.id_ruangan','=','master_ruangans.id')
                        // LEFT JOIN MASTER RUANGAN
                        ->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                        // lEFT JOIN PENJADWALN
                        ->where('jadwal_dosens.id_dosen',$id_dosen)
                        //WHERE ID DOSEN = ID DOSEN LOGIN
                        ->where(DB::raw('CONCAT(jadwal_dosens.tanggal, " ", jadwal_dosens.waktu_selesai)'),'>=',$waktu)
                        // JADWAL YANG DIAMBIL ADALAH JADWAL YANG AKAN DATANG, JADWAL YANG SUDAH LEWAT TIDAK AKAN TAMPIL
                        ->where('jadwal_dosens.status_jadwal',0)
                        // YANG DITAMPILKAN HANYA JADWAL YANG BELUM TERLAKSANA
                        ->orderBy(DB::raw('CONCAT(jadwal_dosens.tanggal, " ", jadwal_dosens.waktu_mulai)', 'ASC'))
                        // DITAMPILKAN BERDASARKAN WAKTU TERDEKAT
                        ->groupBy('jadwal_dosens.id_jadwal');// GROUP BY ID JADWAL

                        return $query_list_jadwal_dosen;
    }	

    public function scopeSearchJadwalDosen($query_search_jadwal_dosen,$id_dosen,$search){

      $waktu = date("Y-m-d H:i:s");// waktu sekarang

      $query_search_jadwal_dosen->select('penjadwalans.id_materi AS id_materi','penjadwalans.tipe_jadwal AS tipe_jadwal','jadwal_dosens.id_mata_kuliah AS id_mata_kuliah','jadwal_dosens.id_jadwal AS id_jadwal','jadwal_dosens.id_ruangan AS id_ruangan','jadwal_dosens.tanggal AS tanggal', 'jadwal_dosens.waktu_mulai AS waktu_mulai', 'jadwal_dosens.waktu_selesai AS waktu_selesai','master_mata_kuliahs.nama_mata_kuliah','master_ruangans.nama_ruangan AS ruangan','master_ruangans.longitude AS longitude','master_ruangans.latitude AS latitude','master_ruangans.batas_jarak_absen AS batas_jarak_absen','penjadwalans.tipe_jadwal AS tipe_jadwal')// DATA YANG DIAMBIL TANGGAL,WAKTU MULAI, WAKTU SELESAI, NAMA MATA KULIAH, RUANGAN, LATITUDE , LONGITUDE, TIPE JADWAL

                        ->leftJoin('master_mata_kuliahs','jadwal_dosens.id_mata_kuliah','=','master_mata_kuliahs.id')
                        //LEFT JOIN KE TABLE MATA KULIAH
                        ->leftJoin('master_ruangans','jadwal_dosens.id_ruangan','=','master_ruangans.id')
                        // LEFT JOIN MASTER RUANGAN
                        ->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                        // lEFT JOIN PENJADWALN
                        ->where('jadwal_dosens.id_dosen',$id_dosen)
                        //WHERE ID DOSEN = ID DOSEN LOGIN
                        ->where(DB::raw('CONCAT(jadwal_dosens.tanggal, " ", jadwal_dosens.waktu_selesai)'),'>=',$waktu)
                        // JADWAL YANG DIAMBIL ADALAH JADWAL YANG AKAN DATANG, JADWAL YANG SUDAH LEWAT TIDAK AKAN TAMPIL
                        ->where('jadwal_dosens.status_jadwal',0)                        
                        // YANG DITAMPILKAN HANYA JADWAL YANG BELUM TERLAKSANA  
                        ->where(function($query_search_jadwal_dosen) use ($search){// search
                            $query_search_jadwal_dosen
                                  ->orWhere('jadwal_dosens.tanggal','LIKE',$search.'%')// OR LIKE TANGGAL
                                  ->orWhere(DB::raw('DATE_FORMAT(jadwal_dosens.tanggal, "%d/%m/%Y")'),'LIKE',$search.'%')// OR LIKE FORMAT TANGGAL d/m/y
                                  ->orWhere(DB::raw('DATE_FORMAT(jadwal_dosens.tanggal, "%d-%m-%Y")'),'LIKE',$search.'%')// OR LIKE FORMAT TANGGAL d-m-y
                                  ->orWhere('jadwal_dosens.waktu_mulai','LIKE',$search.'%')// OR LIKE WAKTU MULAI
                                  ->orWhere('master_mata_kuliahs.nama_mata_kuliah','LIKE',$search.'%')// OR LIKE NAMA MATA KULIAH
                                  ->orWhere('master_ruangans.nama_ruangan','LIKE',$search.'%')  //OR LIKE NAMA RUANGAN
                                  ->orWhere('penjadwalans.tipe_jadwal','LIKE',$search.'%');  //OR LIKE TIPE JADWAL
                        })    // search  
                        ->orderBy(DB::raw('CONCAT(jadwal_dosens.tanggal, " ", jadwal_dosens.waktu_mulai)', 'ASC'))
                        // DITAMPILKAN BERDASARKAN WAKTU TERDEKAT
                        ->groupBy('jadwal_dosens.id_jadwal');// GROUP BY ID JADWAL

                        return $query_search_jadwal_dosen;
    }	


}
