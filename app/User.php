<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','no_hp','alamat','status','id_angkatan','id_role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role(){
        return $this->hasOne('App\User_otoritas','user_id','id');
    }  

    public function angkatan(){
        return $this->hasOne('App\Angkatan','id','id_angkatan');
    }

    // LAP. REKAP PRESENSI MAHASISWA SEMUA TIPE JADWAL
    public function scopeLaporanRekapSemua($query_rekap_semua, $request){

        $query_rekap_semua->select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block',
                                    DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])

                            ->leftJoin('mahasiswa_block','users.id','=','mahasiswa_block.id_mahasiswa')
                            ->leftJoin('master_blocks','users.id_angkatan','=','master_blocks.id_angkatan')
                            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                            ->where('role_user.role_id',3)
                            ->where('master_blocks.id', $request->id_block)
                            ->orwhere('mahasiswa_block.id_block', $request->id_block)
                            ->groupBy('users.id')->get();

        return $query_rekap_semua;

    }

    // LAP. REKAP PRESENSI MAHASISWA TIPE JADWAL : KULIAH, PLENO DAN PRAKTIKUM
    public function scopeLaporanRekapKuliahPlenoPraktikum($query_rekap_kuliah_pleno_praktikum,$request){

        //JIKA MAHASISWA SAJA YG KOSONG
            if ($request->tipe_jadwal != "" AND $request->mahasiswa == "") {
        
                $query_rekap_kuliah_pleno_praktikum->select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
                    ->leftJoin('mahasiswa_block','users.id','=','mahasiswa_block.id_mahasiswa')
                    ->leftJoin('master_blocks','users.id_angkatan','=','master_blocks.id_angkatan')
                    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')                
                    ->leftJoin('penjadwalans', 'master_blocks.id', '=', 'penjadwalans.id_block')
                    ->where('role_user.role_id',3)
                    ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                    ->where('master_blocks.id', $request->id_block)
                    ->orwhere('mahasiswa_block.id_block', $request->id_block)
                    ->groupBy('users.id')->get();
                }
        //JIKA TIPE JADWAL SAJA YG KOSONG
            elseif ($request->tipe_jadwal == "" AND $request->mahasiswa != "") {

                $query_rekap_kuliah_pleno_praktikum->select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
                    ->leftJoin('mahasiswa_block','users.id','=','mahasiswa_block.id_mahasiswa')
                    ->leftJoin('master_blocks','users.id_angkatan','=','master_blocks.id_angkatan')
                    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                    ->where('role_user.role_id',3)
                    ->where('users.id', $request->mahasiswa)
                    ->where('master_blocks.id', $request->id_block)
                    ->orwhere('mahasiswa_block.id_mahasiswa', $request->mahasiswa)
                    ->groupBy('users.id')->get();
            }
        //JIKA SEMUA DIISI
            else{

                $query_rekap_kuliah_pleno_praktikum->select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
                    ->leftJoin('mahasiswa_block','users.id','=','mahasiswa_block.id_mahasiswa')
                    ->leftJoin('master_blocks','users.id_angkatan','=','master_blocks.id_angkatan')
                    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')                
                    ->leftJoin('penjadwalans', 'master_blocks.id', '=', 'penjadwalans.id_block')
                    ->where('role_user.role_id',3)
                    ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                    ->where('users.id', $request->mahasiswa)
                    ->where('master_blocks.id', $request->id_block)
                    ->orwhere('mahasiswa_block.id_mahasiswa', $request->mahasiswa)
                    ->groupBy('users.id')->get();
            }

        return $query_rekap_kuliah_pleno_praktikum;

    }

    // LAP. REKAP PRESENSI MAHASISWA TIPE JADWAL : CSL DAN TUTORIAL
    public function scopeLaporanRekapCslTutorial($query_rekap_csl_tutor,$request){

        //JIKA KELOMPOK DAN MAHASISWA KOSONG
            if ($request->id_kelompok == "" AND $request->mahasiswa == "") {

                $query_rekap_csl_tutor->select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
                    ->leftJoin('mahasiswa_block','users.id','=','mahasiswa_block.id_mahasiswa')
                    ->leftJoin('master_blocks','users.id_angkatan','=','master_blocks.id_angkatan')
                    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')                
                    ->leftJoin('penjadwalans', 'master_blocks.id', '=', 'penjadwalans.id_block')
                    ->where('role_user.role_id',3)
                    ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                    ->where('master_blocks.id', $request->id_block)
                    ->orwhere('mahasiswa_block.id_block', $request->id_block)
                    ->groupBy('users.id')->get();
            }

        //JIKA MAHASISWA SAJA YANG KOSONG
            elseif ($request->id_kelompok != "" AND $request->mahasiswa == "") {        

                $query_rekap_csl_tutor->select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'list_kelompok_mahasiswas.id_kelompok_mahasiswa AS id_kelompok_mahasiswa'])
                    ->leftJoin('list_kelompok_mahasiswas','users.id','=','list_kelompok_mahasiswas.id_mahasiswa')
                    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                    ->leftJoin('penjadwalans', 'list_kelompok_mahasiswas.id_kelompok_mahasiswa', '=', 'penjadwalans.id_kelompok')
                    ->where('role_user.role_id',3)
                    ->where('list_kelompok_mahasiswas.id_kelompok_mahasiswa', $request->id_kelompok)
                    ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                    ->groupBy('users.id')
                    ->get();
            }

        //JIKA MAHASISWA SAJA YANG KOSONG
            elseif ($request->id_kelompok == "" AND $request->mahasiswa != "") {                

              $query_rekap_csl_tutor->select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
                     ->leftJoin('mahasiswa_block','users.id','=','mahasiswa_block.id_mahasiswa')
                     ->leftJoin('master_blocks','users.id_angkatan','=','master_blocks.id_angkatan')
                     ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')                
                     ->leftJoin('penjadwalans', 'master_blocks.id', '=', 'penjadwalans.id_block')
                     ->where('role_user.role_id',3)
                     ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                     ->where('users.id', $request->mahasiswa)
                     ->where('master_blocks.id', $request->id_block)
                     ->orwhere('mahasiswa_block.id_mahasiswa', $request->mahasiswa)
                     ->groupBy('users.id')->get();
            }
        //JIKA SEMUA FILTER DIPILIH
            else{
        
                $query_rekap_csl_tutor->select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'list_kelompok_mahasiswas.id_kelompok_mahasiswa AS id_kelompok_mahasiswa'])
                    ->leftJoin('list_kelompok_mahasiswas','users.id','=','list_kelompok_mahasiswas.id_mahasiswa')
                    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                    ->leftJoin('penjadwalans', 'list_kelompok_mahasiswas.id_kelompok_mahasiswa', '=', 'penjadwalans.id_kelompok')
                    ->where('role_user.role_id',3)
                    ->where('list_kelompok_mahasiswas.id_kelompok_mahasiswa', $request->id_kelompok)
                    ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                    ->where('list_kelompok_mahasiswas.id_mahasiswa', $request->mahasiswa)
                    ->groupBy('users.id')
                    ->get();
            }

        return $query_rekap_csl_tutor;

    }


//DOWNLOAD LAPORAN EXCEL //DOWNLOAD LAPORAN EXCEL //DOWNLOAD LAPORAN EXCEL //DOWNLOAD LAPORAN EXCEL //DOWNLOAD LAPORAN EXCEL //DOWNLOAD LAPORAN EXCEL //DOWNLOAD LAPORAN EXCEL //DOWNLOAD LAPORAN EXCEL

    // DOWNLOAD REKAP PRESENSI MAHASISWA TIPE JADWAL : KULIAH, PLENO DAN PRAKTIKUM
    public function scopeDownloadLaporanRekapKuliahPlenoPraktikum($download_rekap_kuliah_pleno_praktikum, $request){
      
          if ($request->tipe_jadwal == "0" AND $request->mahasiswa == "0") {
            
                    $download_rekap_kuliah_pleno_praktikum->select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
                    ->leftJoin('mahasiswa_block','users.id','=','mahasiswa_block.id_mahasiswa')
                    ->leftJoin('master_blocks','users.id_angkatan','=','master_blocks.id_angkatan')
                    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                    ->where('role_user.role_id',3)
                    ->where('master_blocks.id', $request->id_block)
                    ->orwhere('mahasiswa_block.id_block', $request->id_block)
                    ->groupBy('users.id')->get();
          }
      //JIKA MAHASISWA SAJA YG KOSONG
          elseif ($request->tipe_jadwal != "0" AND $request->mahasiswa == "0") {
            
                    $download_rekap_kuliah_pleno_praktikum->select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
                    ->leftJoin('mahasiswa_block','users.id','=','mahasiswa_block.id_mahasiswa')
                    ->leftJoin('master_blocks','users.id_angkatan','=','master_blocks.id_angkatan')
                    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')                
                    ->leftJoin('penjadwalans', 'master_blocks.id', '=', 'penjadwalans.id_block')
                    ->where('role_user.role_id',3)
                    ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                    ->where('master_blocks.id', $request->id_block)
                    ->orwhere('mahasiswa_block.id_block', $request->id_block)
                    ->groupBy('users.id')->get();
          }
      //JIKA MAHASISWA SAJA YG KOSONG
          elseif ($request->tipe_jadwal == "0" AND $request->mahasiswa != "0") {
            
                    $download_rekap_kuliah_pleno_praktikum->select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
                    ->leftJoin('mahasiswa_block','users.id','=','mahasiswa_block.id_mahasiswa')
                    ->leftJoin('master_blocks','users.id_angkatan','=','master_blocks.id_angkatan')
                    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                    ->where('role_user.role_id',3)
                    ->where('users.id', $request->mahasiswa)
                    ->where('master_blocks.id', $request->id_block)
                    ->orwhere('mahasiswa_block.id_mahasiswa', $request->mahasiswa)
                    ->groupBy('users.id')->get();

          }
      //JIKA SEMUA DIISI
          else{
            
                    $download_rekap_kuliah_pleno_praktikum->select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
                    ->leftJoin('mahasiswa_block','users.id','=','mahasiswa_block.id_mahasiswa')
                    ->leftJoin('master_blocks','users.id_angkatan','=','master_blocks.id_angkatan')
                    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')                
                    ->leftJoin('penjadwalans', 'master_blocks.id', '=', 'penjadwalans.id_block')
                    ->where('role_user.role_id',3)
                    ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                    ->where('users.id', $request->mahasiswa)
                    ->where('master_blocks.id', $request->id_block)
                    ->orwhere('mahasiswa_block.id_mahasiswa', $request->mahasiswa)
                    ->groupBy('users.id')->get();
          }

        return $download_rekap_kuliah_pleno_praktikum;
    }

    // DOWNLOAD REKAP PRESENSI MAHASISWA TIPE JADWAL : CSL DAN TUTORIAL
    public function scopeDownloadRekapCslTutorial($query_download_rekap_csl_tutor,$request){

        //JIKA KELOMPOK DAN MAHASISWA KOSONG
            if ($request->id_kelompok == "0" AND $request->mahasiswa == "0") {

                $query_download_rekap_csl_tutor->select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
                    ->leftJoin('mahasiswa_block','users.id','=','mahasiswa_block.id_mahasiswa')
                    ->leftJoin('master_blocks','users.id_angkatan','=','master_blocks.id_angkatan')
                    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')                
                    ->leftJoin('penjadwalans', 'master_blocks.id', '=', 'penjadwalans.id_block')
                    ->where('role_user.role_id',3)
                    ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                    ->where('master_blocks.id', $request->id_block)
                    ->orwhere('mahasiswa_block.id_block', $request->id_block)
                    ->groupBy('users.id')->get();
            }

        //JIKA MAHASISWA SAJA YANG KOSONG
            elseif ($request->id_kelompok != "0" AND $request->mahasiswa == "0") {        

                $query_download_rekap_csl_tutor->select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'list_kelompok_mahasiswas.id_kelompok_mahasiswa AS id_kelompok_mahasiswa'])
                    ->leftJoin('list_kelompok_mahasiswas','users.id','=','list_kelompok_mahasiswas.id_mahasiswa')
                    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                    ->leftJoin('penjadwalans', 'list_kelompok_mahasiswas.id_kelompok_mahasiswa', '=', 'penjadwalans.id_kelompok')
                    ->where('role_user.role_id',3)
                    ->where('list_kelompok_mahasiswas.id_kelompok_mahasiswa', $request->id_kelompok)
                    ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                    ->groupBy('users.id')
                    ->get();
            }

        //JIKA MAHASISWA SAJA YANG KOSONG
            elseif ($request->id_kelompok == "0" AND $request->mahasiswa != "0") {                

              $query_download_rekap_csl_tutor->select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
                     ->leftJoin('mahasiswa_block','users.id','=','mahasiswa_block.id_mahasiswa')
                     ->leftJoin('master_blocks','users.id_angkatan','=','master_blocks.id_angkatan')
                     ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')                
                     ->leftJoin('penjadwalans', 'master_blocks.id', '=', 'penjadwalans.id_block')
                     ->where('role_user.role_id',3)
                     ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                     ->where('users.id', $request->mahasiswa)
                     ->where('master_blocks.id', $request->id_block)
                     ->orwhere('mahasiswa_block.id_mahasiswa', $request->mahasiswa)
                     ->groupBy('users.id')->get();
            }
        //JIKA SEMUA FILTER DIPILIH
            else{
        
                $query_download_rekap_csl_tutor->select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'list_kelompok_mahasiswas.id_kelompok_mahasiswa AS id_kelompok_mahasiswa'])
                    ->leftJoin('list_kelompok_mahasiswas','users.id','=','list_kelompok_mahasiswas.id_mahasiswa')
                    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                    ->leftJoin('penjadwalans', 'list_kelompok_mahasiswas.id_kelompok_mahasiswa', '=', 'penjadwalans.id_kelompok')
                    ->where('role_user.role_id',3)
                    ->where('list_kelompok_mahasiswas.id_kelompok_mahasiswa', $request->id_kelompok)
                    ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                    ->where('list_kelompok_mahasiswas.id_mahasiswa', $request->mahasiswa)
                    ->groupBy('users.id')
                    ->get();
            }

        return $query_download_rekap_csl_tutor;

    }

}
