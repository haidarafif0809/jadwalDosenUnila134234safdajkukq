<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Penjadwalan;
use App\PresensiMahasiswa;
use App\User;
use App\Master_block;
use App\Materi;;
use App\MahasiswaBlock;
use Auth;
use Excel;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use Jenssegers\Agent\Agent;

class LaporanRekapPresensiMahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        //MENAMPILKAN MAHSISWA
          $mahasiswa = DB::table('users')
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id',3)
            ->pluck('name','id');

        return view('laporan_presensi_mahasiswa.index', ['mahasiswa'=> $mahasiswa]);
    }


//PROSES LAPORAN REKAP
    public function proses_laporan_rekap(Request $request){

      

      if ($request->tipe_jadwal == "" AND $request->mahasiswa == "") {

        $data_angkatan = Master_block::select('id_angkatan')->where('id', $request->id_block)->first();
                $data_mahasiswa = User::select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
                ->leftJoin('mahasiswa_block','users.id','=','mahasiswa_block.id_mahasiswa')
                ->leftJoin('master_blocks','users.id_angkatan','=','master_blocks.id_angkatan')
                ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                ->where('role_user.role_id',3)
                ->where('master_blocks.id', $request->id_block)
                ->orwhere('mahasiswa_block.id_block', $request->id_block)
                ->groupBy('users.id')->get();

                return Datatables::of($data_mahasiswa)

            //JUMLAH SELURUH JADWAL TERLAKSANA
                    ->addColumn('jumlah_jadwal', function($jumlah_jadwal)use($request){

                            if ($jumlah_jadwal->id_block != $request->id_block) {

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)
                              ->where('penjadwalans.id_block', $jumlah_jadwal->id_block_mahasiswa)->count();
                            }
                            else{

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)                        
                              ->where('penjadwalans.id_block', $jumlah_jadwal->id_block)->count();
                            }

                        return $data_jadwal;
                })

            //JUMLAH JADWAL DIHADIRI USER (MAHASISWA)
                    ->addColumn('jumlah_hadir', function($jumlah_hadir)use($request){

                      if ($jumlah_hadir->id_block != $request->id_block) {
                        $data_user_hadir = PresensiMahasiswa::where('id_user',$jumlah_hadir->id)->where('id_block',$jumlah_hadir->id_block_mahasiswa)->count();
                      }
                      else{
                        $data_user_hadir = PresensiMahasiswa::where('id_user',$jumlah_hadir->id)->where('id_block',$jumlah_hadir->id_block)->count();
                      }                        

                        return $data_user_hadir;
                })

            //PRESENTASE KEHADIRAN USER (MAHASISWA)
                    ->addColumn('presentase', function($presentase)use($request){
                        // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
                            if ($presentase->id_block != $request->id_block) {

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)
                              ->where('penjadwalans.id_block', $presentase->id_block_mahasiswa)->count();
                            }
                            else{

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)                        
                              ->where('penjadwalans.id_block', $presentase->id_block)->count();
                            }

                        // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
                            if ($presentase->id_block != $request->id_block) {
                              $data_user_hadir = PresensiMahasiswa::where('id_user',$presentase->id)->where('id_block',$presentase->id_block_mahasiswa)->count();
                            }
                            else{
                              $data_user_hadir = PresensiMahasiswa::where('id_user',$presentase->id)->where('id_block',$presentase->id_block)->count();
                            }

                        if ($data_jadwal == "" AND $data_user_hadir =="") {
                            $data_presentase = 0;
                        }
                        elseif ($data_jadwal != "" AND $data_user_hadir =="") {
                            $data_presentase = 0;
                        }
                        elseif ($data_jadwal == "" AND $data_user_hadir !="") {
                            $data_presentase = 100;
                        }
                        else{
                            $data_presentase = ($data_user_hadir / $data_jadwal) * 100;

                            if ($data_presentase > 100) {
                              $data_presentase = 100;
                            }
                        }

                        return round($data_presentase, 2)."%";
                })

            //KETERANGAN UJIAN USER (MAHASISWA)
                    ->addColumn('keterangan', function($keterangan)use($request){
                      // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)    
                            if ($keterangan->id_block != $request->id_block) {

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)
                              ->where('penjadwalans.id_block', $keterangan->id_block_mahasiswa)->count();
                            }
                            else{

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)                        
                              ->where('penjadwalans.id_block', $keterangan->id_block)->count();
                            }


                         // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
                            if ($keterangan->id_block != $request->id_block) {
                              $data_user_hadir = PresensiMahasiswa::where('id_user',$keterangan->id)->where('id_block',$keterangan->id_block_mahasiswa)->count();
                            }
                            else{
                              $data_user_hadir = PresensiMahasiswa::where('id_user',$keterangan->id)->where('id_block',$keterangan->id_block)->count();
                            }

                    //JIKA HASIL PRESENTASE 0
                        if ($data_jadwal == "" OR $data_user_hadir =="") {
                            $presentase = 0;
                        }
                        else{
                            $presentase = ($data_user_hadir / $data_jadwal) * 100;
                        }                        

                    //LOGIKA KETERNAGAN UJIAN / BOLEH UJIAN
                        if (round($presentase) >= 80 || $data_user_hadir >= $data_jadwal) {
                            $data_keterangan = '<b style="color:green"> <span class="glyphicon glyphicon-ok-sign"></span> BOLEH UJIAN </b>';
                        }
                        else{
                            $data_keterangan = '<b style="color:red"> <span class="glyphicon glyphicon-remove-sign"></span> TIDAK BOLEH UJIAN </b>';
                        }

                        return $data_keterangan;
                })->make(true);
      }
  //JIKA MAHASISWA SAJA YG KOSONG
      elseif ($request->tipe_jadwal != "" AND $request->mahasiswa == "") {

        $data_angkatan = Master_block::select('id_angkatan')->where('id', $request->id_block)->first();
                $data_mahasiswa = User::select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
                ->leftJoin('mahasiswa_block','users.id','=','mahasiswa_block.id_mahasiswa')
                ->leftJoin('master_blocks','users.id_angkatan','=','master_blocks.id_angkatan')
                ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')                
                ->leftJoin('penjadwalans', 'master_blocks.id', '=', 'penjadwalans.id_block')
                ->where('role_user.role_id',3)
                ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                ->where('master_blocks.id', $request->id_block)
                ->orwhere('mahasiswa_block.id_block', $request->id_block)
                ->groupBy('users.id')->get();

            return Datatables::of($data_mahasiswa)

            //JUMLAH SELURUH JADWAL TERLAKSANA
                    ->addColumn('jumlah_jadwal', function($jumlah_jadwal)use($request){

                            if ($jumlah_jadwal->id_block != $request->id_block) {

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)
                              ->where('penjadwalans.id_block', $jumlah_jadwal->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
                            }
                            else{

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)                        
                              ->where('penjadwalans.id_block', $jumlah_jadwal->id_block)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
                            }

                        return $data_jadwal;
                })

            //JUMLAH JADWAL DIHADIRI USER (MAHASISWA)
                    ->addColumn('jumlah_hadir', function($jumlah_hadir)use($request){

                      if ($jumlah_hadir->id_block != $request->id_block) {
                        $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                        ->where('id_user',$jumlah_hadir->id)->where('presensi_mahasiswas.id_block',$jumlah_hadir->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                        ->count();
                      }
                      else{
                        $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                        ->where('id_user',$jumlah_hadir->id)->where('presensi_mahasiswas.id_block',$jumlah_hadir->id_block)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                        ->count();
                      }                        

                        return $data_user_hadir;
                })

            //PRESENTASE KEHADIRAN USER (MAHASISWA)
                    ->addColumn('presentase', function($presentase)use($request){
                        // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
                            if ($presentase->id_block != $request->id_block) {

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)
                              ->where('penjadwalans.id_block', $presentase->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
                            }
                            else{

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)                        
                              ->where('penjadwalans.id_block', $presentase->id_block)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
                            }

                        // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
                            if ($presentase->id_block != $request->id_block) {
                              $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                              ->where('id_user',$presentase->id)->where('presensi_mahasiswas.id_block',$presentase->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                              ->count();
                            }
                            else{
                              $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                              ->where('id_user',$presentase->id)->where('presensi_mahasiswas.id_block',$presentase->id_block)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                              ->count();
                            }

                        if ($data_jadwal == "" AND $data_user_hadir =="") {
                            $data_presentase = 0;
                        }
                        elseif ($data_jadwal != "" AND $data_user_hadir =="") {
                            $data_presentase = 0;
                        }
                        elseif ($data_jadwal == "" AND $data_user_hadir !="") {
                            $data_presentase = 100;
                        }
                        else{
                            $data_presentase = ($data_user_hadir / $data_jadwal) * 100;

                            if ($data_presentase > 100) {
                              $data_presentase = 100;
                            }
                        }

                        return round($data_presentase, 2)."%";
                })

            //KETERANGAN UJIAN USER (MAHASISWA)
                    ->addColumn('keterangan', function($keterangan)use($request){
                      // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)    
                            if ($keterangan->id_block != $request->id_block) {

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)
                              ->where('penjadwalans.id_block', $keterangan->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
                            }
                            else{

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)                        
                              ->where('penjadwalans.id_block', $keterangan->id_block)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
                            }


                         // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
                            if ($keterangan->id_block != $request->id_block) {
                              $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                              ->where('id_user',$keterangan->id)->where('presensi_mahasiswas.id_block',$keterangan->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                              ->count();
                            }
                            else{
                              $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                              ->where('id_user',$keterangan->id)->where('presensi_mahasiswas.id_block',$keterangan->id_block)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                              ->count();
                            }

                    //JIKA HASIL PRESENTASE 0
                        if ($data_jadwal == "" OR $data_user_hadir =="") {
                            $presentase = 0;
                        }
                        else{
                            $presentase = ($data_user_hadir / $data_jadwal) * 100;
                        }                        

                    //LOGIKA KETERNAGAN UJIAN / BOLEH UJIAN
                        if (round($presentase) >= 80 || $data_user_hadir >= $data_jadwal) {
                            $data_keterangan = '<b style="color:green"> <span class="glyphicon glyphicon-ok-sign"></span> BOLEH UJIAN </b>';
                        }
                        else{
                            $data_keterangan = '<b style="color:red"> <span class="glyphicon glyphicon-remove-sign"></span> TIDAK BOLEH UJIAN </b>';
                        }

                        return $data_keterangan;
                })->make(true);
      }
  //JIKA MAHASISWA SAJA YG KOSONG
      elseif ($request->tipe_jadwal == "" AND $request->mahasiswa != "") {

        $data_angkatan = Master_block::select('id_angkatan')->where('id', $request->id_block)->first();
                $data_mahasiswa = User::select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
                ->leftJoin('mahasiswa_block','users.id','=','mahasiswa_block.id_mahasiswa')
                ->leftJoin('master_blocks','users.id_angkatan','=','master_blocks.id_angkatan')
                ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                ->where('role_user.role_id',3)
                ->where('users.id', $request->mahasiswa)
                ->where('master_blocks.id', $request->id_block)
                ->orwhere('mahasiswa_block.id_mahasiswa', $request->mahasiswa)
                ->groupBy('users.id')->get();

                return Datatables::of($data_mahasiswa)

            //JUMLAH SELURUH JADWAL TERLAKSANA
                    ->addColumn('jumlah_jadwal', function($jumlah_jadwal)use($request){

                            if ($jumlah_jadwal->id_block != $request->id_block) {

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)
                              ->where('penjadwalans.id_block', $jumlah_jadwal->id_block_mahasiswa)->count();
                            }
                            else{

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)                        
                              ->where('penjadwalans.id_block', $jumlah_jadwal->id_block)->count();
                            }

                        return $data_jadwal;
                })

            //JUMLAH JADWAL DIHADIRI USER (MAHASISWA)
                    ->addColumn('jumlah_hadir', function($jumlah_hadir)use($request){

                      if ($jumlah_hadir->id_block != $request->id_block) {
                        $data_user_hadir = PresensiMahasiswa::where('id_user',$jumlah_hadir->id)->where('presensi_mahasiswas.id_block',$jumlah_hadir->id_block_mahasiswa)->count();
                      }
                      else{
                        $data_user_hadir = PresensiMahasiswa::where('id_user',$jumlah_hadir->id)->where('presensi_mahasiswas.id_block',$jumlah_hadir->id_block)->count();
                      }                        

                        return $data_user_hadir;
                })

            //PRESENTASE KEHADIRAN USER (MAHASISWA)
                    ->addColumn('presentase', function($presentase)use($request){
                        // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
                            if ($presentase->id_block != $request->id_block) {

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)
                              ->where('penjadwalans.id_block', $presentase->id_block_mahasiswa)->count();
                            }
                            else{

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)                        
                              ->where('penjadwalans.id_block', $presentase->id_block)->count();
                            }

                        // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
                            if ($presentase->id_block != $request->id_block) {
                              $data_user_hadir = PresensiMahasiswa::where('id_user',$presentase->id)->where('presensi_mahasiswas.id_block',$presentase->id_block_mahasiswa)->count();
                            }
                            else{
                              $data_user_hadir = PresensiMahasiswa::where('id_user',$presentase->id)->where('presensi_mahasiswas.id_block',$presentase->id_block)->count();
                            }

                        if ($data_jadwal == "" AND $data_user_hadir =="") {
                            $data_presentase = 0;
                        }
                        elseif ($data_jadwal != "" AND $data_user_hadir =="") {
                            $data_presentase = 0;
                        }
                        elseif ($data_jadwal == "" AND $data_user_hadir !="") {
                            $data_presentase = 100;
                        }
                        else{
                            $data_presentase = ($data_user_hadir / $data_jadwal) * 100;

                            if ($data_presentase > 100) {
                              $data_presentase = 100;
                            }
                        }

                        return round($data_presentase, 2)."%";
                })

            //KETERANGAN UJIAN USER (MAHASISWA)
                    ->addColumn('keterangan', function($keterangan)use($request){
                      // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)    
                            if ($keterangan->id_block != $request->id_block) {

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)
                              ->where('penjadwalans.id_block', $keterangan->id_block_mahasiswa)->count();
                            }
                            else{

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)                        
                              ->where('penjadwalans.id_block', $keterangan->id_block)->count();
                            }


                         // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
                            if ($keterangan->id_block != $request->id_block) {
                              $data_user_hadir = PresensiMahasiswa::where('id_user',$keterangan->id)->where('presensi_mahasiswas.id_block',$keterangan->id_block_mahasiswa)->count();
                            }
                            else{
                              $data_user_hadir = PresensiMahasiswa::where('id_user',$keterangan->id)->where('presensi_mahasiswas.id_block',$keterangan->id_block)->count();
                            }

                    //JIKA HASIL PRESENTASE 0
                        if ($data_jadwal == "" OR $data_user_hadir =="") {
                            $presentase = 0;
                        }
                        else{
                            $presentase = ($data_user_hadir / $data_jadwal) * 100;
                        }                        

                    //LOGIKA KETERNAGAN UJIAN / BOLEH UJIAN
                        if (round($presentase) >= 80 || $data_user_hadir >= $data_jadwal) {
                            $data_keterangan = '<b style="color:green"> <span class="glyphicon glyphicon-ok-sign"></span> BOLEH UJIAN </b>';
                        }
                        else{
                            $data_keterangan = '<b style="color:red"> <span class="glyphicon glyphicon-remove-sign"></span> TIDAK BOLEH UJIAN </b>';
                        }

                        return $data_keterangan;
                })->make(true);

      }
  //JIKA SEMUA DIISI
      else{

        $data_angkatan = Master_block::select('id_angkatan')->where('id', $request->id_block)->first();
                $data_mahasiswa = User::select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
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

            return Datatables::of($data_mahasiswa)

            //JUMLAH SELURUH JADWAL TERLAKSANA
                    ->addColumn('jumlah_jadwal', function($jumlah_jadwal)use($request){

                            if ($jumlah_jadwal->id_block != $request->id_block) {

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)->where('penjadwalans.id_block', $jumlah_jadwal->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
                            }
                            else{

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)->where('penjadwalans.id_block', $jumlah_jadwal->id_block)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
                            }

                        return $data_jadwal;
                })

            //JUMLAH JADWAL DIHADIRI USER (MAHASISWA)
                    ->addColumn('jumlah_hadir', function($jumlah_hadir)use($request){

                      if ($jumlah_hadir->id_block != $request->id_block) {
                        $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                        ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
                        ->where('id_user',$jumlah_hadir->id)->where('presensi_mahasiswas.id_block',$jumlah_hadir->id_block_mahasiswa)
                        ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                        ->count();
                      }
                      else{
                        $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                        ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
                        ->where('id_user',$jumlah_hadir->id)->where('presensi_mahasiswas.id_block',$jumlah_hadir->id_block)
                        ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                        ->count();
                      }                        

                        return $data_user_hadir;
                })

            //PRESENTASE KEHADIRAN USER (MAHASISWA)
                    ->addColumn('presentase', function($presentase)use($request){
                        // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
                            if ($presentase->id_block != $request->id_block) {

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)
                              ->where('penjadwalans.id_block', $presentase->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
                            }
                            else{

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)                        
                              ->where('penjadwalans.id_block', $presentase->id_block)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
                            }

                        // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
                            if ($presentase->id_block != $request->id_block) {
                              $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                              ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
                              ->where('id_user',$presentase->id)->where('presensi_mahasiswas.id_block',$presentase->id_block_mahasiswa)
                              ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                              ->count();
                            }
                            else{
                              $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                              ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
                              ->where('id_user',$presentase->id)->where('presensi_mahasiswas.id_block',$presentase->id_block)
                              ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                              ->count();
                            }

                        if ($data_jadwal == "" AND $data_user_hadir =="") {
                            $data_presentase = 0;
                        }
                        elseif ($data_jadwal != "" AND $data_user_hadir =="") {
                            $data_presentase = 0;
                        }
                        elseif ($data_jadwal == "" AND $data_user_hadir !="") {
                            $data_presentase = 100;
                        }
                        else{
                            $data_presentase = ($data_user_hadir / $data_jadwal) * 100;

                            if ($data_presentase > 100) {
                              $data_presentase = 100;
                            }
                        }

                        return round($data_presentase, 2)."%";
                })

            //KETERANGAN UJIAN USER (MAHASISWA)
                    ->addColumn('keterangan', function($keterangan)use($request){
                      // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)    
                            if ($keterangan->id_block != $request->id_block) {

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)
                              ->where('penjadwalans.id_block', $keterangan->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
                            }
                            else{

                              $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)                        
                              ->where('penjadwalans.id_block', $keterangan->id_block)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
                            }


                         // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
                            if ($keterangan->id_block != $request->id_block) {
                              $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                              ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
                              ->where('id_user',$keterangan->id)->where('presensi_mahasiswas.id_block',$keterangan->id_block_mahasiswa)
                              ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                              ->count();
                            }
                            else{
                              $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                              ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
                              ->where('id_user',$keterangan->id)->where('presensi_mahasiswas.id_block',$keterangan->id_block)
                              ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                              ->count();
                            }

                    //JIKA HASIL PRESENTASE 0
                        if ($data_jadwal == "" OR $data_user_hadir =="") {
                            $presentase = 0;
                        }
                        else{
                            $presentase = ($data_user_hadir / $data_jadwal) * 100;
                        }                        

                    //LOGIKA KETERNAGAN UJIAN / BOLEH UJIAN
                        if (round($presentase) >= 80 || $data_user_hadir >= $data_jadwal) {
                            $data_keterangan = '<b style="color:green"> <span class="glyphicon glyphicon-ok-sign"></span> BOLEH UJIAN </b>';
                        }
                        else{
                            $data_keterangan = '<b style="color:red"> <span class="glyphicon glyphicon-remove-sign"></span> TIDAK BOLEH UJIAN </b>';
                        }

                        return $data_keterangan;
                })->make(true);
      }


                
    } //END CLASS LAP REKAP

// PROSES DOWNLOAD EXCEL
    public function download_lap_rekap_presensi(Request $request, $id_block, $jenis_laporan, $tipe_jadwal, $mahasiswa) {

      

      if ($request->tipe_jadwal == "0" AND $request->mahasiswa == "0") {

        $data_angkatan = Master_block::select('id_angkatan')->where('id', $request->id_block)->first();
                $data_mahasiswa = User::select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
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

        $data_angkatan = Master_block::select('id_angkatan')->where('id', $request->id_block)->first();
                $data_mahasiswa = User::select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
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

        $data_angkatan = Master_block::select('id_angkatan')->where('id', $request->id_block)->first();
                $data_mahasiswa = User::select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
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

        $data_angkatan = Master_block::select('id_angkatan')->where('id', $request->id_block)->first();
                $data_mahasiswa = User::select(['users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
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

        Excel::create('Rekap Presensi Mahasiswa', function($excel) use ($data_mahasiswa, $request) {
          // Set property
          $excel->sheet('Rekap Presensi Mahasiswa', function($sheet) use ($data_mahasiswa, $request) {
            $row = 1;
            $sheet->row($row, [

              'NPM',
              'Nama Mahasiswa',
              'Jumlah Jadwal',
              'Jumlah Hadir',
              'Presentase',
              'Keterangan',

            ]);

             
        foreach ($data_mahasiswa as $data_mahasiswas){

      //JUMLAH SELURUH JADWAL TERLAKSANA
        if ($request->tipe_jadwal == "0" AND $request->mahasiswa == "0") {

          //JUMLAH SELURUH JADWAL TERLAKSANA
              if ($data_mahasiswas->id_block != $request->id_block) {
                $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)->where('penjadwalans.id_block', $data_mahasiswas->id_block_mahasiswa)->count();
              }
              else{
                $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)->where('penjadwalans.id_block', $data_mahasiswas->id_block)->count();
              }

          //JUMLAH JADWAL DIHADIRI USER (MAHASISWA)              
              if ($data_mahasiswas->id_block != $request->id_block) {
                $data_user_hadir = PresensiMahasiswa::where('id_user',$data_mahasiswas->id)->where('id_block',$data_mahasiswas->id_block_mahasiswa)->count();
              }
              else{
                $data_user_hadir = PresensiMahasiswa::where('id_user',$data_mahasiswas->id)->where('id_block',$data_mahasiswas->id_block)->count();
              }

          //PRESENTASE KEHADIRAN USER (MAHASISWA)
              if ($data_jadwal == "" AND $data_user_hadir =="") {
                $data_presentase = 0;
              }
              elseif ($data_jadwal != "" AND $data_user_hadir =="") {
                $data_presentase = 0;
              }
              elseif ($data_jadwal == "" AND $data_user_hadir !="") {
                $data_presentase = 100;
              }
              else{
                $data_presentase = ($data_user_hadir / $data_jadwal) * 100;
                if ($data_presentase > 100) {
                  $data_presentase = 100;
                }
              }

              $data_presentase = round($data_presentase, 2);

          //KETERANGAN UJIAN USER (MAHASISWA)
              //JIKA HASIL PRESENTASE 0
              if ($data_jadwal == "" OR $data_user_hadir =="") {
                $presentase = 0;
              }
              else{
                $presentase = ($data_user_hadir / $data_jadwal) * 100;
              }

              //LOGIKA KETERNAGAN UJIAN / BOLEH UJIAN
              if (round($presentase) >= 80 || $data_user_hadir >= $data_jadwal) {
                $data_keterangan = 'BOLEH UJIAN';
              }
              else{
                $data_keterangan = 'TIDAK BOLEH UJIAN';
              }

        }
      //JIKA MAHASISWA SAJA YG KOSONG
        elseif ($request->tipe_jadwal != "0" AND $request->mahasiswa == "0") {

          //JUMLAH SELURUH JADWAL TERLAKSANA
              if ($data_mahasiswas->id_block != $request->id_block) {
                $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)->where('penjadwalans.id_block', $data_mahasiswas->id_block_mahasiswa)
                ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
              }
              else{
                $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)->where('penjadwalans.id_block', $data_mahasiswas->id_block)
                ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
              }

          //JUMLAH JADWAL DIHADIRI USER (MAHASISWA)              
              if ($data_mahasiswas->id_block != $request->id_block) {
                $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                ->where('id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                ->count();
              }
              else{
                $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                ->where('id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                ->count();
              }              

          //PRESENTASE KEHADIRAN USER (MAHASISWA)
              if ($data_jadwal == "" AND $data_user_hadir =="") {
                $data_presentase = 0;
              }
              elseif ($data_jadwal != "" AND $data_user_hadir =="") {
                $data_presentase = 0;
              }
              elseif ($data_jadwal == "" AND $data_user_hadir !="") {
                $data_presentase = 100;
              }
              else{
                $data_presentase = ($data_user_hadir / $data_jadwal) * 100;
                if ($data_presentase > 100) {
                  $data_presentase = 100;
                }
              }

              $data_presentase = round($data_presentase, 2);

          //KETERANGAN UJIAN USER (MAHASISWA)
              //JIKA HASIL PRESENTASE 0
              if ($data_jadwal == "" OR $data_user_hadir =="") {
                $presentase = 0;
              }
              else{
                $presentase = ($data_user_hadir / $data_jadwal) * 100;
              }

              //LOGIKA KETERNAGAN UJIAN / BOLEH UJIAN
              if (round($presentase) >= 80 || $data_user_hadir >= $data_jadwal) {
                $data_keterangan = 'BOLEH UJIAN';
              }
              else{
                $data_keterangan = 'TIDAK BOLEH UJIAN';
              }

        }
      //JIKA MAHASISWA SAJA YG KOSONG
        elseif ($request->tipe_jadwal == "0" AND $request->mahasiswa != "0") {

          //JUMLAH SELURUH JADWAL TERLAKSANA
              if ($data_mahasiswas->id_block != $request->id_block) {
                $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)->where('penjadwalans.id_block', $data_mahasiswas->id_block_mahasiswa)->count();
              }
              else{
                $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)->where('penjadwalans.id_block', $data_mahasiswas->id_block)->count();
              }

          //JUMLAH JADWAL DIHADIRI USER (MAHASISWA)              
              if ($data_mahasiswas->id_block != $request->id_block) {
                $data_user_hadir = PresensiMahasiswa::where('id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block_mahasiswa)->count();
              }
              else{
                $data_user_hadir = PresensiMahasiswa::where('id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block)->count();
              }              

          //PRESENTASE KEHADIRAN USER (MAHASISWA)
              if ($data_jadwal == "" AND $data_user_hadir =="") {
                $data_presentase = 0;
              }
              elseif ($data_jadwal != "" AND $data_user_hadir =="") {
                $data_presentase = 0;
              }
              elseif ($data_jadwal == "" AND $data_user_hadir !="") {
                $data_presentase = 100;
              }
              else{
                $data_presentase = ($data_user_hadir / $data_jadwal) * 100;
                if ($data_presentase > 100) {
                  $data_presentase = 100;
                }
              }

              $data_presentase = round($data_presentase, 2);

          //KETERANGAN UJIAN USER (MAHASISWA)
              //JIKA HASIL PRESENTASE 0
              if ($data_jadwal == "" OR $data_user_hadir =="") {
                $presentase = 0;
              }
              else{
                $presentase = ($data_user_hadir / $data_jadwal) * 100;
              }

              //LOGIKA KETERNAGAN UJIAN / BOLEH UJIAN
              if (round($presentase) >= 80 || $data_user_hadir >= $data_jadwal) {
                $data_keterangan = 'BOLEH UJIAN';
              }
              else{
                $data_keterangan = 'TIDAK BOLEH UJIAN';
              }

        }
      //JIKA SEMUA DIISI
        else{

          //JUMLAH SELURUH JADWAL TERLAKSANA
              if ($data_mahasiswas->id_block != $request->id_block) {
                $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)->where('penjadwalans.id_block', $data_mahasiswas->id_block_mahasiswa)
                ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
              }
              else{
                $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)->where('penjadwalans.id_block', $data_mahasiswas->id_block)
                ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
              }

          //JUMLAH JADWAL DIHADIRI USER (MAHASISWA)              
              if ($data_mahasiswas->id_block != $request->id_block) {
                $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
                ->where('id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block_mahasiswa)
                ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                ->count();
              }
              else{
                $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
                ->where('id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block)
                ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
                ->count();
              }              

          //PRESENTASE KEHADIRAN USER (MAHASISWA)
              if ($data_jadwal == "" AND $data_user_hadir =="") {
                $data_presentase = 0;
              }
              elseif ($data_jadwal != "" AND $data_user_hadir =="") {
                $data_presentase = 0;
              }
              elseif ($data_jadwal == "" AND $data_user_hadir !="") {
                $data_presentase = 100;
              }
              else{
                $data_presentase = ($data_user_hadir / $data_jadwal) * 100;
                if ($data_presentase > 100) {
                  $data_presentase = 100;
                }
              }

              $data_presentase = round($data_presentase, 2);


 
          //KETERANGAN UJIAN USER (MAHASISWA)
              //JIKA HASIL PRESENTASE 0
              if ($data_jadwal == "" OR $data_user_hadir =="") {
                $presentase = 0;
              }
              else{
                $presentase = ($data_user_hadir / $data_jadwal) * 100;
              }

              //LOGIKA KETERNAGAN UJIAN / BOLEH UJIAN
              if (round($presentase) >= 80 || $data_user_hadir >= $data_jadwal) {
                $data_keterangan = 'BOLEH UJIAN';
              }
              else{
                $data_keterangan = 'TIDAK BOLEH UJIAN';
              }

        }

            $sheet->row(++$row, [
                $data_mahasiswas->email,
                $data_mahasiswas->name,
                $data_jadwal,
                $data_user_hadir,
                $data_presentase."%",
                $data_keterangan,
            ]); 


      }
      
      });

    })->export('xls');

    
}

                        /////////////////////////////////////////////////////////////PROSES LAPORAN DETAIL/////////////////////////////////////////////////////////////

//PROSES LAPORAN DETAIL
    public function proses_laporan_detail(Request $request){

      

//JIKA TIPE JADWAL DAN MAHASISWA KOSONG
      if ($request->tipe_jadwal == "" AND $request->mahasiswa == "") {
                  
                $data_presensi =PresensiMahasiswa::select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block'])
                ->leftJoin('master_blocks','presensi_mahasiswas.id_block','=','master_blocks.id')
                ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
                ->leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
                ->leftJoin('master_mata_kuliahs', 'penjadwalans.id_mata_kuliah', '=', 'master_mata_kuliahs.id')
                ->leftJoin('master_ruangans', 'presensi_mahasiswas.id_ruangan', '=', 'master_ruangans.id')
                ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                ->where('role_user.role_id',3)
                ->where('master_blocks.id', $request->id_block)->get();

      }
  //JIKA MAHASISWA SAJA YG KOSONG
      elseif ($request->tipe_jadwal != "" AND $request->mahasiswa == "") {

        //JIKA TIPE JADWAL YG DIPILIH CSL ATAU TUTORIAL
        if ($request->tipe_jadwal == "CSL" OR $request->tipe_jadwal == "TUTORIAL") {

          //JIKA KOLOM KELOMPOK TIDAK KOSONG (DIPILIH)
          if ($request->id_kelompok != "") {

                $data_presensi =PresensiMahasiswa::select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block'])
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
                ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->get();
          }
          //JIKA KOLOM KELOMPOK KOSONG (TIDAK DIPILIH)
          else{

                $data_presensi =PresensiMahasiswa::select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block'])
                ->leftJoin('master_blocks','presensi_mahasiswas.id_block','=','master_blocks.id')
                ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
                ->leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
                ->leftJoin('master_mata_kuliahs', 'penjadwalans.id_mata_kuliah', '=', 'master_mata_kuliahs.id')
                ->leftJoin('master_ruangans', 'presensi_mahasiswas.id_ruangan', '=', 'master_ruangans.id')
                ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                ->where('role_user.role_id',3)
                ->where('master_blocks.id', $request->id_block)
                ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->get();

          }

        }
        //JIKA TIPE JADWAL YG DIPILIH BUKAN CSL ATAU TUTORIAL
        else{


                $data_presensi =PresensiMahasiswa::select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block'])
                ->leftJoin('master_blocks','presensi_mahasiswas.id_block','=','master_blocks.id')
                ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
                ->leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
                ->leftJoin('master_mata_kuliahs', 'penjadwalans.id_mata_kuliah', '=', 'master_mata_kuliahs.id')
                ->leftJoin('master_ruangans', 'presensi_mahasiswas.id_ruangan', '=', 'master_ruangans.id')
                ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                ->where('role_user.role_id',3)
                ->where('master_blocks.id', $request->id_block)
                ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->get();
        }

      }
  //JIKA MAHASISWA SAJA YG KOSONG
      elseif ($request->tipe_jadwal == "" AND $request->mahasiswa != "") {

                $data_presensi =PresensiMahasiswa::select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block', 'penjadwalans.id_mata_kuliah AS id_mata_kuliah'])
                ->leftJoin('master_blocks','presensi_mahasiswas.id_block','=','master_blocks.id')
                ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
                ->leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
                ->leftJoin('master_mata_kuliahs', 'penjadwalans.id_mata_kuliah', '=', 'master_mata_kuliahs.id')
                ->leftJoin('master_ruangans', 'presensi_mahasiswas.id_ruangan', '=', 'master_ruangans.id')
                ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                ->where('role_user.role_id',3)
                ->where('master_blocks.id', $request->id_block)
                ->where('users.id', $request->mahasiswa)->get();
      }
  //JIKA SEMUA DIISI
      else{

                $data_presensi =PresensiMahasiswa::select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block', 'penjadwalans.id_mata_kuliah AS id_mata_kuliah'])
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
                ->where('users.id', $request->mahasiswa)->get();
      }

                      return Datatables::of($data_presensi)

            //KETERANGAN UJIAN USER (MAHASISWA)
                    ->addColumn('keterangan', function($keterangan){                     

                    //LOGIKA KETERNAGAN UJIAN / BOLEH UJIAN
                      $data_hadir = PresensiMahasiswa::where('id',$keterangan->id_presensi)->where('id_block',$keterangan->id_block)->count();

                        if ($data_hadir > 0) {
                            $data_keterangan = '<b style="color:green"> <span class="glyphicon glyphicon-ok-sign"></span> MASUK </b>';
                        }
                        else{
                            $data_keterangan = '<b style="color:red"> <span class="glyphicon glyphicon-remove-sign"></span> BELUM MASUK </b>';
                        }

                        return $data_keterangan;
                })
 
            //WAKTU ABSEN
                ->editColumn('waktu',function($waktu){
                  if ($waktu->waktu == "" ) {
                     return "-";
                   } 
                   else{
                    return $waktu->waktu; 
                   }
                })
 
            //JARAK ABSEN
                ->editColumn('jarak_absen',function($jarak){                  
                    return $jarak->jarak_absen." m";
                })
 
            //FOTO ABSEN
                ->addColumn('foto',function($foto){
                  if ($foto->foto == "") {
                    return "";
                  }
                  else{
                    return view('laporan_presensi_mahasiswa._foto_absen', ['foto'=> $foto]);
                  }                
                })->make(true);


    }// END CLASS FUNCTION PROSES LAP DETAIL


    // PROSES DOWNLOAD EXCEL LAPORAN DETAIL
    public function download_lap_detail_presensi(Request $request, $id_block, $jenis_laporan, $tipe_jadwal, $mahasiswa) {

      


//JIKA TIPE JADWAL DAN MAHASISWA KOSONG
      if ($request->tipe_jadwal == "0" AND $request->mahasiswa == "0") {
                  
                $data_presensi =PresensiMahasiswa::select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block', 'penjadwalans.id_mata_kuliah AS id_mata_kuliah'])
                ->leftJoin('master_blocks','presensi_mahasiswas.id_block','=','master_blocks.id')
                ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
                ->leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
                ->leftJoin('master_mata_kuliahs', 'penjadwalans.id_mata_kuliah', '=', 'master_mata_kuliahs.id')
                ->leftJoin('master_ruangans', 'presensi_mahasiswas.id_ruangan', '=', 'master_ruangans.id')
                ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                ->where('role_user.role_id',3)
                ->where('master_blocks.id', $request->id_block)->get();
                

      }
  //JIKA MAHASISWA SAJA YG KOSONG
      elseif ($request->tipe_jadwal != "0" AND $request->mahasiswa == "0") {

        //JIKA TIPE JADWAL YG DIPILIH CSL ATAU TUTORIAL
        if ($request->tipe_jadwal == "CSL" OR $request->tipe_jadwal == "TUTORIAL") {

          //JIKA KOLOM KELOMPOK TIDAK KOSONG (DIPILIH)
          if ($request->id_kelompok != "") {

                $data_presensi =PresensiMahasiswa::select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block', 'penjadwalans.id_mata_kuliah AS id_mata_kuliah'])
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
                ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->get();
          }
          //JIKA KOLOM KELOMPOK KOSONG (TIDAK DIPILIH)
          else{

                $data_presensi =PresensiMahasiswa::select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block', 'penjadwalans.id_mata_kuliah AS id_mata_kuliah'])
                ->leftJoin('master_blocks','presensi_mahasiswas.id_block','=','master_blocks.id')
                ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
                ->leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
                ->leftJoin('master_mata_kuliahs', 'penjadwalans.id_mata_kuliah', '=', 'master_mata_kuliahs.id')
                ->leftJoin('master_ruangans', 'presensi_mahasiswas.id_ruangan', '=', 'master_ruangans.id')
                ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                ->where('role_user.role_id',3)
                ->where('master_blocks.id', $request->id_block)
                ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->get();

          }

        }
        //JIKA TIPE JADWAL YG DIPILIH BUKAN CSL ATAU TUTORIAL
        else{


                $data_presensi =PresensiMahasiswa::select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block', 'penjadwalans.id_mata_kuliah AS id_mata_kuliah'])
                ->leftJoin('master_blocks','presensi_mahasiswas.id_block','=','master_blocks.id')
                ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
                ->leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
                ->leftJoin('master_mata_kuliahs', 'penjadwalans.id_mata_kuliah', '=', 'master_mata_kuliahs.id')
                ->leftJoin('master_ruangans', 'presensi_mahasiswas.id_ruangan', '=', 'master_ruangans.id')
                ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                ->where('role_user.role_id',3)
                ->where('master_blocks.id', $request->id_block)
                ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->get();
        }
      }
  //JIKA MAHASISWA SAJA YG KOSONG
      elseif ($request->tipe_jadwal == "0" AND $request->mahasiswa != "0") {

                $data_presensi =PresensiMahasiswa::select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block', 'penjadwalans.id_mata_kuliah AS id_mata_kuliah'])
                ->leftJoin('master_blocks','presensi_mahasiswas.id_block','=','master_blocks.id')
                ->leftJoin('users', 'presensi_mahasiswas.id_user', '=', 'users.id')
                ->leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
                ->leftJoin('materis','penjadwalans.id_materi','=','materis.id')
                ->leftJoin('master_mata_kuliahs', 'penjadwalans.id_mata_kuliah', '=', 'master_mata_kuliahs.id')
                ->leftJoin('master_ruangans', 'presensi_mahasiswas.id_ruangan', '=', 'master_ruangans.id')
                ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                ->where('role_user.role_id',3)
                ->where('master_blocks.id', $request->id_block)
                ->where('users.id', $request->mahasiswa)->get();
      }
  //JIKA SEMUA DIISI
      else{

                $data_presensi =PresensiMahasiswa::select(['presensi_mahasiswas.id AS id_presensi','presensi_mahasiswas.id_user AS id_user',DB::raw('IFNULL(presensi_mahasiswas.jarak_ke_lokasi_absen, "-") AS jarak_absen'), 'presensi_mahasiswas.foto AS foto', 'master_mata_kuliahs.nama_mata_kuliah AS mata_kuliah', 'materis.nama_materi AS nama_materi', DB::raw('IFNULL(master_ruangans.nama_ruangan, "-") AS nama_ruangan'), 'presensi_mahasiswas.created_at AS waktu', 'users.email AS email', 'users.name AS name', 'master_blocks.id AS id_block', 'penjadwalans.id_mata_kuliah AS id_mata_kuliah'])
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
                ->where('users.id', $request->mahasiswa)->get();
      }


    Excel::create('Detail Presensi Mahasiswa', function($excel) use ($data_presensi) {
      // Set property
      $excel->sheet('Detail Presensi Mahasiswa', function($sheet) use ($data_presensi) {
              
              $sheet->loadView('laporan_presensi_mahasiswa.export_laporan_detail_presensi', ['data_presensi' => $data_presensi ]);
           
      
      });

    })->export('xls');

    
}// END CLASS LAPORAN DETAIL

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
