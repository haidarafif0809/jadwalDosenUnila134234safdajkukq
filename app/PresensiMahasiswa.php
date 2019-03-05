<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PresensiMahasiswa extends Model
{
    protected $fillable = ['id_user', 'id_ruangan', 'id_jadwal', 'longitude' , 'latitude' , 'foto', 'jarak_ke_lokasi_absen', 'id_block', 'id_kelompok'];

   // LAP. PRESENSI MAHASISWA SUDAH ABSEN
    public function scopeMahasiswaHadir($mahasiswa_sudah_absen, $request){

        if ($request->tipe_jadwal == "CSL" OR $request->tipe_jadwal == "TUTORIAL") {

          $data_jadwal =Penjadwalan::select('id_kelompok')->where('id', $request->id)->first();

          $mahasiswa_sudah_absen->select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block', 'penjadwalans.id_mata_kuliah AS id_mata_kuliah', 'penjadwalans.tipe_jadwal AS tipe_jadwal'])
          ->leftJoin('master_blocks','presensi_mahasiswas.id_block','=','master_blocks.id')
          ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
          ->leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
          ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
          ->leftJoin('kelompok_mahasiswas','presensi_mahasiswas.id_kelompok','=','kelompok_mahasiswas.id')
          ->leftJoin('master_mata_kuliahs', 'penjadwalans.id_mata_kuliah', '=', 'master_mata_kuliahs.id')
          ->leftJoin('master_ruangans', 'presensi_mahasiswas.id_ruangan', '=', 'master_ruangans.id')
          ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
          ->where('role_user.role_id',3)
          ->where('kelompok_mahasiswas.id', $data_jadwal->id_kelompok)
          ->where('penjadwalans.id', $request->id)->get();
      }
        //JIKA TIPE JADWAL BUKAN CSL ATAU TUTORIAL
      else{

        $mahasiswa_sudah_absen->select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block', 'penjadwalans.id_mata_kuliah AS id_mata_kuliah', 'penjadwalans.tipe_jadwal AS tipe_jadwal'])
        ->leftJoin('master_blocks','presensi_mahasiswas.id_block','=','master_blocks.id')
        ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
        ->leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
        ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
        ->leftJoin('master_mata_kuliahs', 'penjadwalans.id_mata_kuliah', '=', 'master_mata_kuliahs.id')
        ->leftJoin('master_ruangans', 'presensi_mahasiswas.id_ruangan', '=', 'master_ruangans.id')
        ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
        ->where('role_user.role_id',3)
        ->where('penjadwalans.id', $request->id)->get();
    }

    return $mahasiswa_sudah_absen;

}

public function scopeSemuaTipeSemuaMahasiswa($query,$block){
    return $query->select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block'])
    ->leftJoin('master_blocks','presensi_mahasiswas.id_block','=','master_blocks.id')
    ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
    ->leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
    ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
    ->leftJoin('master_mata_kuliahs', 'penjadwalans.id_mata_kuliah', '=', 'master_mata_kuliahs.id')
    ->leftJoin('master_ruangans', 'presensi_mahasiswas.id_ruangan', '=', 'master_ruangans.id')
    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
    ->where('role_user.role_id',3)
    ->where('master_blocks.id', $block);
}

public function scopeCslTutorPerkelompok($query,$request){
    return $query->select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block'])
    ->leftJoin('master_blocks','presensi_mahasiswas.id_block','=','master_blocks.id')
    ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
    ->leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
    ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
    ->leftJoin('kelompok_mahasiswas','presensi_mahasiswas.id_kelompok','=','kelompok_mahasiswas.id')
    ->leftJoin('master_mata_kuliahs', 'penjadwalans.id_mata_kuliah', '=', 'master_mata_kuliahs.id')
    ->leftJoin('master_ruangans', 'presensi_mahasiswas.id_ruangan', '=', 'master_ruangans.id')
    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
    ->where('role_user.role_id',3)
    ->where('master_blocks.id', $request->id_block)
    ->where('kelompok_mahasiswas.id', $request->id_kelompok)
    ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal);
}

public function scopeCslTutorTidakPerkelompok($query,$request){
    return $query->select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block'])
    ->leftJoin('master_blocks','presensi_mahasiswas.id_block','=','master_blocks.id')
    ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
    ->leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
    ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
    ->leftJoin('master_mata_kuliahs', 'penjadwalans.id_mata_kuliah', '=', 'master_mata_kuliahs.id')
    ->leftJoin('master_ruangans', 'presensi_mahasiswas.id_ruangan', '=', 'master_ruangans.id')
    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
    ->where('role_user.role_id',3)
    ->where('master_blocks.id', $request->id_block)
    ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal);
}

public function scopeKuliahPlenoSemuaMahasiswa($query,$request){
    return $query->select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block'])
    ->leftJoin('master_blocks','presensi_mahasiswas.id_block','=','master_blocks.id')
    ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
    ->leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
    ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
    ->leftJoin('master_mata_kuliahs', 'penjadwalans.id_mata_kuliah', '=', 'master_mata_kuliahs.id')
    ->leftJoin('master_ruangans', 'presensi_mahasiswas.id_ruangan', '=', 'master_ruangans.id')
    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
    ->where('role_user.role_id',3)
    ->where('master_blocks.id', $request->id_block)
    ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal);
}
public function scopeKuliahPlenoPermahasiswa($query,$request){
    return $query->select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block'])
    ->leftJoin('master_blocks','presensi_mahasiswas.id_block','=','master_blocks.id')
    ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
    ->leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
    ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
    ->leftJoin('master_mata_kuliahs', 'penjadwalans.id_mata_kuliah', '=', 'master_mata_kuliahs.id')
    ->leftJoin('master_ruangans', 'presensi_mahasiswas.id_ruangan', '=', 'master_ruangans.id')
    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
    ->where('role_user.role_id',3)
    ->where('master_blocks.id', $request->id_block)
    ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
    ->where('users.id', $request->mahasiswa);
}

public function scopeSemuaTipePermahasiswa($query,$request){
    return $query->select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block', 'penjadwalans.id_mata_kuliah AS id_mata_kuliah'])
    ->leftJoin('master_blocks','presensi_mahasiswas.id_block','=','master_blocks.id')
    ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
    ->leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
    ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
    ->leftJoin('master_mata_kuliahs', 'penjadwalans.id_mata_kuliah', '=', 'master_mata_kuliahs.id')
    ->leftJoin('master_ruangans', 'presensi_mahasiswas.id_ruangan', '=', 'master_ruangans.id')
    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
    ->where('role_user.role_id',3)
    ->where('master_blocks.id', $request->id_block)
    ->where('users.id', $request->mahasiswa);
}
public function scopeCslTutorPerkelompokPermahasiswa($query,$request){
    return $query->select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block'])
    ->leftJoin('master_blocks','presensi_mahasiswas.id_block','=','master_blocks.id')
    ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
    ->leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
    ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
    ->leftJoin('kelompok_mahasiswas','presensi_mahasiswas.id_kelompok','=','kelompok_mahasiswas.id')
    ->leftJoin('master_mata_kuliahs', 'penjadwalans.id_mata_kuliah', '=', 'master_mata_kuliahs.id')
    ->leftJoin('master_ruangans', 'presensi_mahasiswas.id_ruangan', '=', 'master_ruangans.id')
    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
    ->where('role_user.role_id',3)
    ->where('master_blocks.id', $request->id_block)
    ->where('kelompok_mahasiswas.id', $request->id_kelompok)
    ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
    ->where('users.id', $request->mahasiswa);
}

public function scopeCslTutorTidakPerkelompokPermahasiswa($query,$request){
    return $query->select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block', 'penjadwalans.id_mata_kuliah AS id_mata_kuliah'])
    ->leftJoin('master_blocks','presensi_mahasiswas.id_block','=','master_blocks.id')
    ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
    ->leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
    ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
    ->leftJoin('master_mata_kuliahs', 'penjadwalans.id_mata_kuliah', '=', 'master_mata_kuliahs.id')
    ->leftJoin('master_ruangans', 'presensi_mahasiswas.id_ruangan', '=', 'master_ruangans.id')
    ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
    ->where('role_user.role_id',3)
    ->where('master_blocks.id', $request->id_block)
    ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
    ->where('users.id', $request->mahasiswa);
}


}
