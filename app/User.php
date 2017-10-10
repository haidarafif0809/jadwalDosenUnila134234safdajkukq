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
        'name', 'email', 'password','no_hp','alamat','status','id_angkatan','id_role','foto_profil'
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

   // LAP. PRESENSI MAHASISWA BELUM ABSEN
    public function scopeMahasiswaTidakHadir($mahasiswa_belum_absen, $request){


        if ($request->tipe_jadwal == "CSL" OR $request->tipe_jadwal == "TUTORIAL") {

                $data_jadwal =Penjadwalan::select('id_kelompok')->where('id', $request->id)->first();

                $mahasiswa_belum_absen->select(['users.email AS email', 'users.name AS name', 'users.id AS id', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'penjadwalans.tipe_jadwal AS tipe_jadwal'])
                ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                ->leftJoin('master_blocks','users.id_angkatan','=','master_blocks.id_angkatan')
                ->leftJoin('penjadwalans','master_blocks.id','=','penjadwalans.id_block')
                ->leftJoin('list_kelompok_mahasiswas','users.id','=','list_kelompok_mahasiswas.id_mahasiswa')
                ->leftJoin('presensi_mahasiswas', function($leftJoin){
                  $leftJoin->on('penjadwalans.id','=','presensi_mahasiswas.id_jadwal');
                  $leftJoin->on('list_kelompok_mahasiswas.id_mahasiswa','=','presensi_mahasiswas.id_user');
                })                
                ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
                ->leftJoin('master_mata_kuliahs', 'penjadwalans.id_mata_kuliah', '=', 'master_mata_kuliahs.id')
                ->leftJoin('master_ruangans', 'penjadwalans.id_ruangan', '=', 'master_ruangans.id')
                ->where('role_user.role_id',3)
                ->where('list_kelompok_mahasiswas.id_kelompok_mahasiswa', $data_jadwal->id_kelompok)
                ->where('penjadwalans.id',$request->id)
                ->where('presensi_mahasiswas.id', NULL)
                ->get();
        }
        //JIKA TIPE JADWAL BUKAN CSL ATAU TUTORIAL
        else{

                $mahasiswa_belum_absen->select(['users.email AS email', 'users.name AS name', 'users.id AS id', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'penjadwalans.tipe_jadwal AS tipe_jadwal'])
                ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                ->leftJoin('master_blocks','users.id_angkatan','=','master_blocks.id_angkatan')
                ->leftJoin('penjadwalans','master_blocks.id','=','penjadwalans.id_block')
                ->leftJoin('presensi_mahasiswas', function($leftJoin){
                  $leftJoin->on('penjadwalans.id','=','presensi_mahasiswas.id_jadwal');
                  $leftJoin->on('users.id','=','presensi_mahasiswas.id_user');
                })
                ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
                ->leftJoin('master_mata_kuliahs', 'penjadwalans.id_mata_kuliah', '=', 'master_mata_kuliahs.id')
                ->leftJoin('master_ruangans', 'penjadwalans.id_ruangan', '=', 'master_ruangans.id')
                ->where('role_user.role_id',3)
                ->where('penjadwalans.id',$request->id)
                ->where('presensi_mahasiswas.id', NULL)
                ->get();
        }        

        return $mahasiswa_belum_absen;

    }

}
