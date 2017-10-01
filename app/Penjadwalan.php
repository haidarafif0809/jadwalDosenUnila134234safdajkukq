<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Yajra\Auditable\AuditableTrait;
use Illuminate\Support\Facades\DB;

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

    public function scopeJadwalBlockMahasiswa($query,$array_block){
      $waktu = date("Y-m-d H:i:s");
      $hari_ini = date("Y-m-d");

      $query->select('penjadwalans.id AS id_jadwal', 'penjadwalans.id_block AS id_block', 'penjadwalans.id_ruangan AS id_ruangan', 'penjadwalans.tipe_jadwal AS tipe_jadwal', 'penjadwalans.tanggal AS tanggal',  'penjadwalans.waktu_mulai AS waktu_mulai',  'penjadwalans.waktu_selesai AS waktu_selesai', 'master_mata_kuliahs.nama_mata_kuliah', 'master_ruangans.nama_ruangan AS ruangan', 'master_ruangans.longitude AS longitude', 'master_ruangans.latitude AS latitude', 'master_ruangans.batas_jarak_absen AS batas_jarak_absen') ->leftJoin('master_mata_kuliahs','penjadwalans.id_mata_kuliah','=','master_mata_kuliahs.id')
      // DATA YANG DIAMBIL TANGGAL,WAKTU MULAI, WAKTU SELESAI, NAMA MATA KULIAH, DAN RUANGAN
                        //LEFT JOIN KE TABLE MATA KULIAH
                        ->leftJoin('master_ruangans','penjadwalans.id_ruangan','=','master_ruangans.id')
                        // LEFT JOIN MASTER RUANGAN
                        ->whereIn('penjadwalans.id_block', $array_block)
                        //WHERE ID BLOK = ID BLOK USER LOGIN
                        ->where('penjadwalans.tanggal', '=', $hari_ini)
                        // JADWAL YANG TAMPIL ADALAH JADWAL HARI INI
                        ->where(DB::raw('CONCAT(penjadwalans.tanggal, " ", penjadwalans.waktu_selesai)'),'>=',$waktu)
                        // JADWAL YANG DIAMBIL ADALAH JADWAL YANG AKAN DATANG, JADWAL YANG SUDAH LEWAT TIDAK AKAN TAMPIL
                        ->where('penjadwalans.status_jadwal', '<', 2)
                        // YANG DITAMPILKAN HANYA JADWAL YANG BELUM TERLAKSANA
                        ->where('id_kelompok',null)
                        ->orderBy(DB::raw('CONCAT(penjadwalans.tanggal, " ", penjadwalans.waktu_mulai)', 'ASC'));
                        // DITAMPILKAN BERDASARKAN WAKTU TERDEKAT

                        return $query;
    }

    public function scopeJadwalCslTutorMahasiswa($query,$array_kelompok){
  $waktu = date("Y-m-d H:i:s");
      $hari_ini = date("Y-m-d");

      $query->select('penjadwalans.id AS id_jadwal', 'penjadwalans.id_block AS id_block', 'penjadwalans.id_ruangan AS id_ruangan', 'penjadwalans.tipe_jadwal AS tipe_jadwal', 'penjadwalans.tanggal AS tanggal',  'penjadwalans.waktu_mulai AS waktu_mulai',  'penjadwalans.waktu_selesai AS waktu_selesai', 'materis.nama_materi AS materi', 'master_ruangans.nama_ruangan AS ruangan', 'master_ruangans.longitude AS longitude', 'master_ruangans.latitude AS latitude', 'master_ruangans.batas_jarak_absen AS batas_jarak_absen') 

                        ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
      // DATA YANG DIAMBIL TANGGAL,WAKTU MULAI, WAKTU SELESAI, NAMA MATA KULIAH, DAN RUANGAN
                        //LEFT JOIN KE TABLE MATA KULIAH
                        ->leftJoin('master_ruangans','penjadwalans.id_ruangan','=','master_ruangans.id')
                        // LEFT JOIN MASTER RUANGAN
                        ->whereIn('penjadwalans.id_kelompok', $array_kelompok)
                        //WHERE ID BLOK = ID BLOK USER LOGIN
                        ->where('penjadwalans.tanggal', '=', $hari_ini)
                        // JADWAL YANG TAMPIL ADALAH JADWAL HARI INI
                        ->where(DB::raw('CONCAT(penjadwalans.tanggal, " ", penjadwalans.waktu_selesai)'),'>=',$waktu)
                        // JADWAL YANG DIAMBIL ADALAH JADWAL YANG AKAN DATANG, JADWAL YANG SUDAH LEWAT TIDAK AKAN TAMPIL
                        ->where('penjadwalans.status_jadwal', '<', 2)
                        // YANG DITAMPILKAN HANYA JADWAL YANG BELUM TERLAKSANA
                        ->orderBy(DB::raw('CONCAT(penjadwalans.tanggal, " ", penjadwalans.waktu_mulai)', 'ASC'));
                        // DITAMPILKAN BERDASARKAN WAKTU TERDEKAT

                        return $query;
    }

    public function scopeSearchJadwalBlockMahasiswa($query,$array_block,$search){

      $waktu = date("Y-m-d H:i:s");
        $hari_ini = date("Y-m-d");

        $queryselect('penjadwalans.id AS id_jadwal', 'penjadwalans.id_block AS id_block', 'penjadwalans.id_ruangan AS id_ruangan', 'penjadwalans.tipe_jadwal AS tipe_jadwal', 'penjadwalans.tanggal AS tanggal',  'penjadwalans.waktu_mulai AS waktu_mulai',  'penjadwalans.waktu_selesai AS waktu_selesai', 'master_mata_kuliahs.nama_mata_kuliah', 'master_ruangans.nama_ruangan AS ruangan', 'master_ruangans.longitude AS longitude', 'master_ruangans.latitude AS latitude', 'master_ruangans.batas_jarak_absen AS batas_jarak_absen')// DATA YANG DIAMBIL TANGGAL,WAKTU MULAI, WAKTU SELESAI, NAMA MATA KULIAH, DAN RUANGAN

                        ->leftJoin('master_mata_kuliahs','penjadwalans.id_mata_kuliah','=','master_mata_kuliahs.id')
                        //LEFT JOIN KE TABLE MATA KULIAH
                        ->leftJoin('master_ruangans','penjadwalans.id_ruangan','=','master_ruangans.id')
                        // LEFT JOIN MASTER RUANGAN
                        ->whereIn('penjadwalans.id_block', $array_block)
                        //WHERE ID BLOK = ID BLOK USER LOGIN
                        ->where('penjadwalans.tanggal', '=', $hari_ini)
                        // JADWAL YANG TAMPIL ADALAH JADWAL HARI INI
                        ->where(DB::raw('CONCAT(penjadwalans.tanggal, " ", penjadwalans.waktu_selesai)'),'>=',$waktu)
                        // JADWAL YANG DIAMBIL ADALAH JADWAL YANG AKAN DATANG, JADWAL YANG SUDAH LEWAT TIDAK AKAN TAMPIL
                        ->where('penjadwalans.status_jadwal', '<', 2)
                        // YANG DITAMPILKAN HANYA JADWAL YANG BELUM TERLAKSANA
                        ->where(function($query) use ($search){// search
                            $query->orWhere('penjadwalans.tanggal','LIKE',$search.'%')// OR LIKE TANGGAL
                                  ->orWhere(DB::raw('DATE_FORMAT(penjadwalans.tanggal, "%d/%m/%Y")'),'LIKE',$search.'%')// OR LIKE FORMAT TANGGAL d/m/y
                                  ->orWhere(DB::raw('DATE_FORMAT(penjadwalans.tanggal, "%d-%m-%Y")'),'LIKE',$search.'%')// OR LIKE FORMAT TANGGAL d-m-y
                                  ->orWhere('penjadwalans.waktu_mulai','LIKE',$search.'%')// OR LIKE WAKTU MULAI
                                  ->orWhere('penjadwalans.tipe_jadwal','LIKE',$search.'%')// OR LIKE WAKTU MULAI
                                  ->orWhere('master_mata_kuliahs.nama_mata_kuliah','LIKE',$search.'%')// OR LIKE NAMA MATA KULIAH
                                  ->orWhere('master_ruangans.nama_ruangan','LIKE',$search.'%');  //OR LIKE NAMA RUANGAN
                        }) 
                        ->orderBy(DB::raw('CONCAT(penjadwalans.tanggal, " ", penjadwalans.waktu_mulai)', 'ASC'));
                        // DITAMPILKAN BERDASARKAN WAKTU TERDEKAT

              return $query;
                        
    }

    public function scopeSeachJadwalTutorCslMahasiswa($query,$array_kelompok,$search){

       $waktu = date("Y-m-d H:i:s");
        $hari_ini = date("Y-m-d");

        $queryselect('penjadwalans.id AS id_jadwal', 'penjadwalans.id_block AS id_block', 'penjadwalans.id_ruangan AS id_ruangan', 'penjadwalans.tipe_jadwal AS tipe_jadwal', 'penjadwalans.tanggal AS tanggal',  'penjadwalans.waktu_mulai AS waktu_mulai',  'penjadwalans.waktu_selesai AS waktu_selesai', 'master_mata_kuliahs.nama_mata_kuliah', 'master_ruangans.nama_ruangan AS ruangan', 'master_ruangans.longitude AS longitude', 'master_ruangans.latitude AS latitude', 'master_ruangans.batas_jarak_absen AS batas_jarak_absen')// DATA YANG DIAMBIL TANGGAL,WAKTU MULAI, WAKTU SELESAI, NAMA MATA KULIAH, DAN RUANGAN

                        ->leftJoin('master_mata_kuliahs','penjadwalans.id_mata_kuliah','=','master_mata_kuliahs.id')
                        //LEFT JOIN KE TABLE MATA KULIAH
                        ->leftJoin('master_ruangans','penjadwalans.id_ruangan','=','master_ruangans.id')
                        // LEFT JOIN MASTER RUANGAN
                        ->whereIn('penjadwalans.id_block', $array_block)
                        //WHERE ID BLOK = ID BLOK USER LOGIN
                        ->where('penjadwalans.tanggal', '=', $hari_ini)
                        // JADWAL YANG TAMPIL ADALAH JADWAL HARI INI
                        ->where(DB::raw('CONCAT(penjadwalans.tanggal, " ", penjadwalans.waktu_selesai)'),'>=',$waktu)
                        // JADWAL YANG DIAMBIL ADALAH JADWAL YANG AKAN DATANG, JADWAL YANG SUDAH LEWAT TIDAK AKAN TAMPIL
                        ->where('penjadwalans.status_jadwal', '<', 2)
                        // YANG DITAMPILKAN HANYA JADWAL YANG BELUM TERLAKSANA
                        ->where(function($query) use ($search){// search
                            $query->orWhere('penjadwalans.tanggal','LIKE',$search.'%')// OR LIKE TANGGAL
                                  ->orWhere(DB::raw('DATE_FORMAT(penjadwalans.tanggal, "%d/%m/%Y")'),'LIKE',$search.'%')// OR LIKE FORMAT TANGGAL d/m/y
                                  ->orWhere(DB::raw('DATE_FORMAT(penjadwalans.tanggal, "%d-%m-%Y")'),'LIKE',$search.'%')// OR LIKE FORMAT TANGGAL d-m-y
                                  ->orWhere('penjadwalans.waktu_mulai','LIKE',$search.'%')// OR LIKE WAKTU MULAI
                                  ->orWhere('penjadwalans.tipe_jadwal','LIKE',$search.'%')// OR LIKE WAKTU MULAI
                                  ->orWhere('master_mata_kuliahs.nama_mata_kuliah','LIKE',$search.'%')// OR LIKE NAMA MATA KULIAH
                                  ->orWhere('master_ruangans.nama_ruangan','LIKE',$search.'%');  //OR LIKE NAMA RUANGAN
                        }) 
                        ->orderBy(DB::raw('CONCAT(penjadwalans.tanggal, " ", penjadwalans.waktu_mulai)', 'ASC'));
                        // DITAMPILKAN BERDASARKAN WAKTU TERDEKAT

              return $query;

    }

}
