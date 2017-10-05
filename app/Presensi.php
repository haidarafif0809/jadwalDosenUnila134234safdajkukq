<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
	protected $table = 'presensi';
   	protected $fillable = ['id_user', 'id_ruangan', 'id_jadwal', 'longitude' , 'latitude' , 'foto', 'jarak_ke_lokasi_absen','id_block'];


   	public function dosen()
	{
		  	return $this->hasOne('App\User','id','id_user');
	}

	public function penjadwalan()
	{
		  	return $this->hasOne('App\Penjadwalan','id','id_jadwal');
	}

	public function ruangan()
	{
		  	return $this->hasOne('App\Master_ruangan','id','id_ruangan');
	}



    /// LAP. DETAIL PRESENSI DOSEN
    public function scopeDetailPresensiDosen($query_detail_presensi,$request)
    {
                // JIKA DOSEN == SEMUA AND TIPE JADWAL == SEMUA
        if ($request->dosen == 'semua' AND $request->tipe_jadwal == 'SEMUA') {

                $query_detail_presensi->select('penjadwalans.id_materi AS id_materi','users.name AS nama_dosen','master_mata_kuliahs.nama_mata_kuliah AS nama_mata_kuliah','master_ruangans.nama_ruangan AS ruangan','penjadwalans.tipe_jadwal AS tipe_jadwal','presensi.created_at AS waktu','presensi.jarak_ke_lokasi_absen AS jarak_absen','presensi.foto AS foto')
                // SELECT NAMA DOSEN, NAMA MATA KULIAH, RUANGAN, TIPE JADWAL, WAKTU, JARAK ABSEN, FOTO
                                    ->leftJoin('users','presensi.id_user','=','users.id')// LEFT JOIN USER ON ID USER = USER.ID
                                    ->leftJoin('master_ruangans','presensi.id_ruangan','=','master_ruangans.id')// LEFT JOIN MASTER RUANGAN ON ID RUANGAN = RUANGAN.ID
                                    ->leftJoin('penjadwalans','presensi.id_jadwal','=','penjadwalans.id')// LEFT JOIN PENJADAWALAN ON ID JADWAL = PENJADWALN.ID
                                    ->leftJoin('master_mata_kuliahs','penjadwalans.id_mata_kuliah','=','master_mata_kuliahs.id')// LEFT JOIN MATA KULIAH ON ID MATA KULIAH MATAKULIAH.ID
                                    ->where('presensi.id_block',$request->id_block);// AND ID BLOCK

        }else if ($request->dosen == 'semua' AND $request->tipe_jadwal != 'SEMUA') {

              $query_detail_presensi->select('penjadwalans.id_materi AS id_materi','users.name AS nama_dosen','master_mata_kuliahs.nama_mata_kuliah AS nama_mata_kuliah','master_ruangans.nama_ruangan AS ruangan','penjadwalans.tipe_jadwal AS tipe_jadwal','presensi.created_at AS waktu','presensi.jarak_ke_lokasi_absen AS jarak_absen','presensi.foto AS foto')
                // SELECT NAMA DOSEN, NAMA MATA KULIAH, RUANGAN, TIPE JADWAL, WAKTU, JARAK ABSEN, FOTO
                                    ->leftJoin('users','presensi.id_user','=','users.id')// LEFT JOIN USER ON ID USER = USER.ID
                                    ->leftJoin('master_ruangans','presensi.id_ruangan','=','master_ruangans.id')// LEFT JOIN MASTER RUANGAN ON ID RUANGAN = RUANGAN.ID
                                    ->leftJoin('penjadwalans','presensi.id_jadwal','=','penjadwalans.id')// LEFT JOIN PENJADAWALAN ON ID JADWAL = PENJADWALN.ID
                                    ->leftJoin('master_mata_kuliahs','penjadwalans.id_mata_kuliah','=','master_mata_kuliahs.id')// LEFT JOIN MATA KULIAH ON ID MATA KULIAH MATAKULIAH.ID
                                    ->where('presensi.id_block',$request->id_block)// AND ID BLOCK
                                    ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal);// AND TIPE JADWAL

        }else if ($request->dosen != 'semua' AND $request->tipe_jadwal == 'SEMUA') {
          
              $query_detail_presensi->select('penjadwalans.id_materi AS id_materi','users.name AS nama_dosen','master_mata_kuliahs.nama_mata_kuliah AS nama_mata_kuliah','master_ruangans.nama_ruangan AS ruangan','penjadwalans.tipe_jadwal AS tipe_jadwal','presensi.created_at AS waktu','presensi.jarak_ke_lokasi_absen AS jarak_absen','presensi.foto AS foto')
                // SELECT NAMA DOSEN, NAMA MATA KULIAH, RUANGAN, TIPE JADWAL, WAKTU, JARAK ABSEN, FOTO
                                    ->leftJoin('users','presensi.id_user','=','users.id')// LEFT JOIN USER ON ID USER = USER.ID
                                    ->leftJoin('master_ruangans','presensi.id_ruangan','=','master_ruangans.id')// LEFT JOIN MASTER RUANGAN ON ID RUANGAN = RUANGAN.ID
                                    ->leftJoin('penjadwalans','presensi.id_jadwal','=','penjadwalans.id')// LEFT JOIN PENJADAWALAN ON ID JADWAL = PENJADWALN.ID
                                    ->leftJoin('master_mata_kuliahs','penjadwalans.id_mata_kuliah','=','master_mata_kuliahs.id')// LEFT JOIN MATA KULIAH ON ID MATA KULIAH MATAKULIAH.ID
                                    ->where('presensi.id_block',$request->id_block)// AND ID BLOCK
                                    ->where('presensi.id_user',$request->dosen);// AND ID USER
        }
        else{                   

                $query_detail_presensi->select('penjadwalans.id_materi AS id_materi','users.name AS nama_dosen','master_mata_kuliahs.nama_mata_kuliah AS nama_mata_kuliah','master_ruangans.nama_ruangan AS ruangan','penjadwalans.tipe_jadwal AS tipe_jadwal','presensi.created_at AS waktu','presensi.jarak_ke_lokasi_absen AS jarak_absen','presensi.foto AS foto')
                // SELECT NAMA DOSEN, NAMA MATA KULIAH, RUANGAN, TIPE JADWAL, WAKTU, JARAK ABSEN, FOTO
                                    ->leftJoin('users','presensi.id_user','=','users.id')// LEFT JOIN USER ON ID USER = USER.ID
                                    ->leftJoin('master_ruangans','presensi.id_ruangan','=','master_ruangans.id')// LEFT JOIN MASTER RUANGAN ON ID RUANGAN = RUANGAN.ID
                                    ->leftJoin('penjadwalans','presensi.id_jadwal','=','penjadwalans.id')// LEFT JOIN PENJADAWALAN ON ID JADWAL = PENJADWALN.ID
                                    ->leftJoin('master_mata_kuliahs','penjadwalans.id_mata_kuliah','=','master_mata_kuliahs.id')// LEFT JOIN MATA KULIAH ON ID MATA KULIAH MATAKULIAH.ID
                                    ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)// WHERE TIPE JADWAL;
                                    ->where('presensi.id_block',$request->id_block)// AND ID BLOCK
                                    ->where('presensi.id_user',$request->dosen);// AND ID USER
        }

        return $query_detail_presensi;


    }
}
