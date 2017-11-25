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

    public function jumlahJadwalRekap($data_mahasiswa,$request){
      $jumlah_jadwal = array();
      foreach ($data_mahasiswa as $data ) {

        if ($data->id_block != $request->id_block) {
          $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
          ->where('id_user',$data->id)->where('presensi_mahasiswas.id_block',$data->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
          ->count();
        }
        else{
          $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
          ->where('id_user',$data->id)->where('presensi_mahasiswas.id_block',$data->id_block)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
          ->count();
        }                        


        array_push($jumlah_jadwal, $data_user_hadir);


      }
      return max($jumlah_jadwal);

    }


//PROSES LAPORAN REKAP KULIAH, PLENO dan PRAKTIKUM
    public function proses_laporan_rekap(Request $request){

      if ($request->tipe_jadwal != "" AND $request->mahasiswa == "") {

        //PAKAI SCOPE YG ADA DI MODEL USER
        $data_mahasiswa = User::laporanRekapKuliahPlenoPraktikum($request)->get();

        $jumlah_hadir_terbanyak =  $this->jumlahJadwalRekap($data_mahasiswa,$request);

        return Datatables::of($data_mahasiswa)

            //JUMLAH SELURUH JADWAL TERLAKSANA
        ->addColumn('jumlah_jadwal', $jumlah_hadir_terbanyak)

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
        ->addColumn('presentase', function($presentase)use($request, $jumlah_hadir_terbanyak){
                        // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
          $data_jadwal =  $jumlah_hadir_terbanya;

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

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
            ->where('penjadwalans.id_block', $keterangan->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
          }
          else{

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)                        
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

        //PAKAI SCOPE YG ADA DI MODEL USER
        $data_mahasiswa = User::laporanRekapKuliahPlenoPraktikum($request)->get();

        return Datatables::of($data_mahasiswa)

            //JUMLAH SELURUH JADWAL TERLAKSANA
        ->addColumn('jumlah_jadwal', function($jumlah_jadwal)use($request){

          if ($jumlah_jadwal->id_block != $request->id_block) {

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
            ->where('penjadwalans.id_block', $jumlah_jadwal->id_block_mahasiswa)->count();
          }
          else{

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)                        
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

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
            ->where('penjadwalans.id_block', $presentase->id_block_mahasiswa)->count();
          }
          else{

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)                        
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

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
            ->where('penjadwalans.id_block', $keterangan->id_block_mahasiswa)->count();
          }
          else{

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)                        
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

        //PAKAI SCOPE YG ADA DI MODEL USER
        $data_mahasiswa = User::laporanRekapKuliahPlenoPraktikum($request)->get();

        return Datatables::of($data_mahasiswa)

            //JUMLAH SELURUH JADWAL TERLAKSANA
        ->addColumn('jumlah_jadwal', function($jumlah_jadwal)use($request){

          if ($jumlah_jadwal->id_block != $request->id_block) {

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $jumlah_jadwal->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
          }
          else{

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $jumlah_jadwal->id_block)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
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

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
            ->where('penjadwalans.id_block', $presentase->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
          }
          else{

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)                        
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

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
            ->where('penjadwalans.id_block', $keterangan->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
          }
          else{

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)                        
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


//PROSES LAPORAN REKAP CSL dan TUTOR
    public function proses_laporan_rekap_csl_tutor(Request $request){

      if ($request->id_kelompok == "" AND $request->mahasiswa == "") {

      //PAKAI SCOPE YG ADA DI MODEL USER
        $data_mahasiswa = User::laporanRekapCslTutorial($request)->get();

        return Datatables::of($data_mahasiswa)

            //JUMLAH SELURUH JADWAL TERLAKSANA
        ->addColumn('jumlah_jadwal', function($jumlah_jadwal)use($request){

          if ($jumlah_jadwal->id_block != $request->id_block) {

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
            ->where('penjadwalans.id_block', $jumlah_jadwal->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
          }
          else{

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)                        
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

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
            ->where('penjadwalans.id_block', $presentase->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
          }
          else{

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)                        
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

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
            ->where('penjadwalans.id_block', $keterangan->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
          }
          else{

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)                        
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
      elseif ($request->id_kelompok != "" AND $request->mahasiswa == "") {

        //PAKAI SCOPE YG ADA DI MODEL USER
        $data_mahasiswa = User::laporanRekapCslTutorial($request)->get();

        return Datatables::of($data_mahasiswa)

            //JUMLAH SELURUH JADWAL TERLAKSANA
        ->addColumn('jumlah_jadwal', function($jumlah_jadwal)use($request){

          $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
          ->where('penjadwalans.id_kelompok', $jumlah_jadwal->id_kelompok_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();

          return $data_jadwal;
        })

            //JUMLAH JADWAL DIHADIRI USER (MAHASISWA)
        ->addColumn('jumlah_hadir', function($jumlah_hadir)use($request){

          $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
          ->where('id_user',$jumlah_hadir->id)->where('presensi_mahasiswas.id_kelompok',$jumlah_hadir->id_kelompok_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
          ->count(); 

          return $data_user_hadir;
        })

            //PRESENTASE KEHADIRAN USER (MAHASISWA)
        ->addColumn('presentase', function($presentase)use($request){

          $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
          ->where('penjadwalans.id_kelompok', $presentase->id_kelompok_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();

          $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
          ->where('id_user',$presentase->id)->where('presensi_mahasiswas.id_kelompok',$presentase->id_kelompok_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
          ->count();

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

          $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
          ->where('penjadwalans.id_kelompok', $keterangan->id_kelompok_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();

          $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
          ->where('id_user',$keterangan->id)->where('presensi_mahasiswas.id_kelompok',$keterangan->id_kelompok_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
          ->count();
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
      elseif ($request->id_kelompok == "" AND $request->mahasiswa != "") {

        //PAKAI SCOPE YG ADA DI MODEL USER
        $data_mahasiswa = User::laporanRekapCslTutorial($request)->get();

        return Datatables::of($data_mahasiswa)

            //JUMLAH SELURUH JADWAL TERLAKSANA
        ->addColumn('jumlah_jadwal', function($jumlah_jadwal)use($request){

          if ($jumlah_jadwal->id_block != $request->id_block) {

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
            ->where('penjadwalans.id_block', $jumlah_jadwal->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
          }
          else{

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)                        
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

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
            ->where('penjadwalans.id_block', $presentase->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
          }
          else{

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)                        
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

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
            ->where('penjadwalans.id_block', $keterangan->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
          }
          else{

            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)                        
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
      else{

        //PAKAI SCOPE YG ADA DI MODEL USER
        $data_mahasiswa = User::laporanRekapCslTutorial($request)->get();

        return Datatables::of($data_mahasiswa)

            //JUMLAH SELURUH JADWAL TERLAKSANA
        ->addColumn('jumlah_jadwal', function($jumlah_jadwal)use($request){

          $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
          ->where('penjadwalans.id_kelompok', $jumlah_jadwal->id_kelompok_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();

          return $data_jadwal;
        })

            //JUMLAH JADWAL DIHADIRI USER (MAHASISWA)
        ->addColumn('jumlah_hadir', function($jumlah_hadir)use($request){

          $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
          ->where('id_user',$jumlah_hadir->id)->where('presensi_mahasiswas.id_kelompok',$jumlah_hadir->id_kelompok_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
          ->count(); 

          return $data_user_hadir;
        })

            //PRESENTASE KEHADIRAN USER (MAHASISWA)
        ->addColumn('presentase', function($presentase)use($request){

          $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
          ->where('penjadwalans.id_kelompok', $presentase->id_kelompok_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();

          $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
          ->where('id_user',$presentase->id)->where('presensi_mahasiswas.id_kelompok',$presentase->id_kelompok_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
          ->count();

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

          $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
          ->where('penjadwalans.id_kelompok', $keterangan->id_kelompok_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();

          $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
          ->where('id_user',$keterangan->id)->where('presensi_mahasiswas.id_kelompok',$keterangan->id_kelompok_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
          ->count();
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

    }
//END CLASS LAPORAN REKAP CSL dan TUTOR

// PROSES DOWNLOAD EXCEL KULIAH, PLENO, PRAKTIKUM
    public function download_lap_rekap_presensi(Request $request, $id_block, $jenis_laporan, $tipe_jadwal, $mahasiswa, $id_kelompok) {

    //PAKAI SCOPE YG ADA DI MODEL USER
      $data_mahasiswa = User::downloadLaporanRekapKuliahPlenoPraktikum($request)->get();

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

            if ($request->tipe_jadwal != "0" AND $request->mahasiswa == "0") {

          //JUMLAH SELURUH JADWAL TERLAKSANA
              if ($data_mahasiswas->id_block != $request->id_block) {
                $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $data_mahasiswas->id_block_mahasiswa)
                ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
              }
              else{
                $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $data_mahasiswas->id_block)
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
                $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $data_mahasiswas->id_block_mahasiswa)->count();
              }
              else{
                $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $data_mahasiswas->id_block)->count();
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
                $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $data_mahasiswas->id_block_mahasiswa)
                ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();
              }
              else{
                $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $data_mahasiswas->id_block)
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


// PROSES DOWNLOAD EXCEL CSL DAN TUTORIAL
public function download_lap_rekap_csl_tutor_presensi(Request $request, $id_block, $jenis_laporan, $tipe_jadwal, $mahasiswa, $id_kelompok) {

    //PAKAI SCOPE YG ADA DI MODEL USER
  $data_mahasiswa = User::downloadRekapCslTutorial($request)->get();

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

        if ($request->id_kelompok == "0" AND $request->mahasiswa == "0") {

            //JUMLAH SELURUH JADWAL TERLAKSANA
          if ($data_mahasiswas->id_block != $request->id_block) {

              //JADWAL
            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
            ->where('penjadwalans.id_block', $data_mahasiswas->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();

              //PRESENSI
            $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
            ->where('id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
            ->count();
          }
          else{

              //JADWAL
            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
            ->where('penjadwalans.id_block', $data_mahasiswas->id_block)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();

              //PRESENSI
            $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')              
            ->where('id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
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
            //JIKA FILTER KELOMPOK DIPILIH
        elseif ($request->id_kelompok != "0" AND $request->mahasiswa == "0") {

            //JUMLAH SELURUH JADWAL TERLAKSANA
            //JADWAL
          $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
          ->where('penjadwalans.id_kelompok', $data_mahasiswas->id_kelompok_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();

            //PRESENSI
          $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
          ->where('id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_kelompok',$data_mahasiswas->id_kelompok_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
          ->count();


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
            //JIKA MAHASISWA SAJA YANG KOSONG
        elseif ($request->id_kelompok == "0" AND $request->mahasiswa != "0") {   

                          //JUMLAH SELURUH JADWAL TERLAKSANA
          if ($data_mahasiswas->id_block != $request->id_block) {

              //JADWAL
            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
            ->where('penjadwalans.id_block', $data_mahasiswas->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();

              //PRESENSI
            $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
            ->where('id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
            ->count();
          }
          else{

              //JADWAL
            $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
            ->where('penjadwalans.id_block', $data_mahasiswas->id_block)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();

              //PRESENSI
            $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')              
            ->where('id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
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
        else{

            //JUMLAH SELURUH JADWAL TERLAKSANA
            //JADWAL
          $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)
          ->where('penjadwalans.id_kelompok', $data_mahasiswas->id_kelompok_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)->count();

            //PRESENSI
          $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans', 'presensi_mahasiswas.id_jadwal', '=', 'penjadwalans.id')
          ->where('id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_kelompok',$data_mahasiswas->id_kelompok_mahasiswa)->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
          ->count();


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
        ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
        ->where('users.id', $request->mahasiswa)->get();
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
        ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
        ->where('users.id', $request->mahasiswa)->get();

      }

    }

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
  })
                //MATERI ? MATA KULIAH 
  ->editColumn('materi_kuliah',function($materi_kuliah)use($request){
    if ($request->tipe_jadwal == "CSL" OR $request->tipe_jadwal == "TUTORIAL" ) {
     return $materi_kuliah = $materi_kuliah->nama_materi;
   } 
   else{
     return $materi_kuliah = $materi_kuliah->mata_kuliah;
   }
 })->make(true);


    }// END CLASS FUNCTION PROSES LAP DETAIL


    // PROSES DOWNLOAD EXCEL LAPORAN DETAIL
    public function download_lap_detail_presensi(Request $request, $id_block, $jenis_laporan, $tipe_jadwal, $mahasiswa, $id_kelompok) {



//JIKA TIPE JADWAL DAN MAHASISWA KOSONG
      if ($request->tipe_jadwal == "0" AND $request->mahasiswa == "0") {

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
      elseif ($request->tipe_jadwal != "0" AND $request->mahasiswa == "0") {

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
            ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
            ->where('users.id', $request->mahasiswa)->get();
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
            ->where('penjadwalans.tipe_jadwal', $request->tipe_jadwal)
            ->where('users.id', $request->mahasiswa)->get();

          }

        }

      }


      Excel::create('Detail Presensi Mahasiswa', function($excel) use ($data_presensi) {
      // Set property
        $excel->sheet('Detail Presensi Mahasiswa', function($sheet) use ($data_presensi) {

          $sheet->loadView('laporan_presensi_mahasiswa.export_laporan_detail_presensi', ['data_presensi' => $data_presensi ]);


        });

      })->export('xls');


}// END CLASS LAPORAN DETAIL

//PROSES LAPORAN REKAP SEMUA TIPE JADWAL
public function proses_laporan_rekap_semua(Request $request){

      //Rekap semua tipe jadwal
  $data_mahasiswa = User::laporanRekapSemua($request)->get();

  return Datatables::of($data_mahasiswa)

    //PRESENTASE KEHADIRAN TIPE JADWAL

        // #CSL
  ->addColumn('persentase_csl', function($persentase_csl)use($request){

          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
    if ($persentase_csl->id_block != $request->id_block) {
      $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $persentase_csl->id_block_mahasiswa)
      ->where('penjadwalans.tipe_jadwal', "CSL")->count();
    }
    else{
      $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $persentase_csl->id_block)
      ->where('penjadwalans.tipe_jadwal', "CSL")->count();
    }

          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
    if ($persentase_csl->id_block != $request->id_block) {
      $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
      ->where('presensi_mahasiswas.id_user',$persentase_csl->id)->where('presensi_mahasiswas.id_block',$persentase_csl->id_block_mahasiswa)
      ->where('penjadwalans.tipe_jadwal', "CSL")->count();
    }
    else{
      $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
      ->where('presensi_mahasiswas.id_user',$persentase_csl->id)->where('presensi_mahasiswas.id_block',$persentase_csl->id_block)
      ->where('penjadwalans.tipe_jadwal', "CSL")->count();
    }

  //JIKA JUMLAH JADWAL DAN ABSEN SAMA
    if ($data_jadwal == $data_user_hadir) {

            //JIKA JADWAL DAN ABSEN SAMA SAMA KOSONG ATAU 0 (BELUM ADA JADWAL DA ABSEN)
      if ($data_jadwal == "0" AND $data_user_hadir == "0") {
        $data_persentase_csl =  "-";
        return $data_persentase_csl;
      }
      else{
        $data_persentase_csl = 100;
      }
    }
    elseif ($data_jadwal != "" AND $data_user_hadir =="") {
      $data_persentase_csl = 0;
    }
    elseif ($data_jadwal == "" AND $data_user_hadir !="") {
      $data_persentase_csl =  "-";
      return $data_persentase_csl;
    }
    else{
      $data_persentase_csl = ($data_user_hadir / $data_jadwal) * 100;

      if ($data_persentase_csl > 100) {
        $data_persentase_csl = 100;
      }
    }

    return round($data_persentase_csl, 2)."%";
  })

        // #KULIAH
  ->addColumn('persentase_kuliah', function($persentase_kuliah)use($request){

          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
    if ($persentase_kuliah->id_block != $request->id_block) {
      $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $persentase_kuliah->id_block_mahasiswa)
      ->where('penjadwalans.tipe_jadwal', "KULIAH")->count();
    }
    else{
      $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $persentase_kuliah->id_block)
      ->where('penjadwalans.tipe_jadwal', "KULIAH")->count();
    }

          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
    if ($persentase_kuliah->id_block != $request->id_block) {
      $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
      ->where('presensi_mahasiswas.id_user',$persentase_kuliah->id)->where('presensi_mahasiswas.id_block',$persentase_kuliah->id_block_mahasiswa)
      ->where('penjadwalans.tipe_jadwal', "KULIAH")->count();
    }
    else{
      $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
      ->where('presensi_mahasiswas.id_user',$persentase_kuliah->id)->where('presensi_mahasiswas.id_block',$persentase_kuliah->id_block)
      ->where('penjadwalans.tipe_jadwal', "KULIAH")->count();
    }
  //JIKA JUMLAH JADWAL DAN ABSEN SAMA
    if ($data_jadwal == $data_user_hadir) {

            //JIKA JADWAL DAN ABSEN SAMA SAMA KOSONG ATAU 0 (BELUM ADA JADWAL DA ABSEN)
      if ($data_jadwal == "0" AND $data_user_hadir == "0") {
        $data_persentase_kuliah = "-";
        return $data_persentase_kuliah;
      }
      else{
        $data_persentase_kuliah = 100;
      }
    }
    elseif ($data_jadwal != "" AND $data_user_hadir =="") {
      $data_persentase_kuliah = 0;
    }
    elseif ($data_jadwal == "" AND $data_user_hadir !="") {
      $data_persentase_kuliah = "-";
      return $data_persentase_kuliah;
    }
    else{
      $data_persentase_kuliah = ($data_user_hadir / $data_jadwal) * 100;

      if ($data_persentase_kuliah > 100) {
        $data_persentase_kuliah = 100;
      }
    }

    return round($data_persentase_kuliah, 2)."%";
  })

        // #PLENO
  ->addColumn('persentase_pleno', function($persentase_pleno)use($request){

          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
    if ($persentase_pleno->id_block != $request->id_block) {
      $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $persentase_pleno->id_block_mahasiswa)
      ->where('penjadwalans.tipe_jadwal', "PLENO")->count();
    }
    else{
      $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $persentase_pleno->id_block)
      ->where('penjadwalans.tipe_jadwal', "PLENO")->count();
    }

          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
    if ($persentase_pleno->id_block != $request->id_block) {
      $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
      ->where('presensi_mahasiswas.id_user',$persentase_pleno->id)->where('presensi_mahasiswas.id_block',$persentase_pleno->id_block_mahasiswa)
      ->where('penjadwalans.tipe_jadwal', "PLENO")->count();
    }
    else{
      $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
      ->where('presensi_mahasiswas.id_user',$persentase_pleno->id)->where('presensi_mahasiswas.id_block',$persentase_pleno->id_block)
      ->where('penjadwalans.tipe_jadwal', "PLENO")->count();
    }


  //JIKA JUMLAH JADWAL DAN ABSEN SAMA
    if ($data_jadwal == $data_user_hadir) {

            //JIKA JADWAL DAN ABSEN SAMA SAMA KOSONG ATAU 0 (BELUM ADA JADWAL DA ABSEN)
      if ($data_jadwal == "0" AND $data_user_hadir == "0") {
        $data_persentase_pleno =  "-";
        return $data_persentase_pleno;
      }
      else{
        $data_persentase_pleno = 100;
      }
    }
    elseif ($data_jadwal != "" AND $data_user_hadir =="") {
      $data_persentase_pleno = 0;
    }
    elseif ($data_jadwal == "" AND $data_user_hadir !="") {
      $data_persentase_kuliah = "-";
      return $data_persentase_kuliah;
    }
    else{
      $data_persentase_pleno = ($data_user_hadir / $data_jadwal) * 100;

      if ($data_persentase_pleno > 100) {
        $data_persentase_pleno = 100;
      }
    }

    return round($data_persentase_pleno, 2)."%";
  })

        // #PRAKTIKUM
  ->addColumn('persentase_praktikum', function($persentase_praktikum)use($request){

          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
    if ($persentase_praktikum->id_block != $request->id_block) {
      $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $persentase_praktikum->id_block_mahasiswa)
      ->where('penjadwalans.tipe_jadwal', "PRAKTIKUM")->count();
    }
    else{
      $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $persentase_praktikum->id_block)
      ->where('penjadwalans.tipe_jadwal', "PRAKTIKUM")->count();
    }

          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
    if ($persentase_praktikum->id_block != $request->id_block) {
      $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
      ->where('presensi_mahasiswas.id_user',$persentase_praktikum->id)->where('presensi_mahasiswas.id_block',$persentase_praktikum->id_block_mahasiswa)
      ->where('penjadwalans.tipe_jadwal', "PRAKTIKUM")->count();
    }
    else{
      $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
      ->where('presensi_mahasiswas.id_user',$persentase_praktikum->id)->where('presensi_mahasiswas.id_block',$persentase_praktikum->id_block)
      ->where('penjadwalans.tipe_jadwal', "PRAKTIKUM")->count();
    }

  //JIKA JUMLAH JADWAL DAN ABSEN SAMA
    if ($data_jadwal == $data_user_hadir) {

            //JIKA JADWAL DAN ABSEN SAMA SAMA KOSONG ATAU 0 (BELUM ADA JADWAL DA ABSEN)
      if ($data_jadwal == "0" AND $data_user_hadir == "0") {
        $data_persentase_praktikum =  "-";
        return $data_persentase_praktikum;
      }
      else{
        $data_persentase_praktikum =  100;
      }

    }
    elseif ($data_jadwal != "" AND $data_user_hadir =="") {
      $data_persentase_praktikum = 0;
    }
            elseif ($data_jadwal == "" AND $data_user_hadir !="") { //JIKA STATUS JADWAL KOSONG ATAU BELUM TERLAKSANA MAKA PERSENTASE "-"
            $data_persentase_praktikum =  "-";
            return $data_persentase_praktikum;
          }
          else{
            $data_persentase_praktikum = ($data_user_hadir / $data_jadwal) * 100;

            if ($data_persentase_praktikum > 100) {
              $data_persentase_praktikum = 100;
            }
          }

          return round($data_persentase_praktikum, 2)."%";
        })

        // #TUTORIAL
  ->addColumn('persentase_tutorial', function($persentase_tutorial)use($request){

          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
    if ($persentase_tutorial->id_block != $request->id_block) {
      $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $persentase_tutorial->id_block_mahasiswa)
      ->where('penjadwalans.tipe_jadwal', "TUTORIAL")->count();
    }
    else{
      $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $persentase_tutorial->id_block)
      ->where('penjadwalans.tipe_jadwal', "TUTORIAL")->count();
    }

          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
    if ($persentase_tutorial->id_block != $request->id_block) {
      $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
      ->where('presensi_mahasiswas.id_user',$persentase_tutorial->id)->where('presensi_mahasiswas.id_block',$persentase_tutorial->id_block_mahasiswa)
      ->where('penjadwalans.tipe_jadwal', "TUTORIAL")->count();
    }
    else{
      $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
      ->where('presensi_mahasiswas.id_user',$persentase_tutorial->id)->where('presensi_mahasiswas.id_block',$persentase_tutorial->id_block)
      ->where('penjadwalans.tipe_jadwal', "TUTORIAL")->count();
    }

  //JIKA JUMLAH JADWAL DAN ABSEN SAMA
    if ($data_jadwal == $data_user_hadir) {

            //JIKA JADWAL DAN ABSEN SAMA SAMA KOSONG ATAU 0 (BELUM ADA JADWAL DA ABSEN)
      if ($data_jadwal == "0" AND $data_user_hadir == "0") {
        $data_persentase_tutorial = "-";
        return $data_persentase_tutorial;
      }
      else{
        $data_persentase_tutorial =  100;
      }
    }
    elseif ($data_jadwal != "" AND $data_user_hadir =="") {
      $data_persentase_tutorial = 0;
    }
    elseif ($data_jadwal == "" AND $data_user_hadir !="") {
      $data_persentase_kuliah = "-";
      return $data_persentase_kuliah;
    }
    else{
      $data_persentase_tutorial = ($data_user_hadir / $data_jadwal) * 100;

      if ($data_persentase_tutorial > 100) {
        $data_persentase_tutorial = 100;
      }
    }

    return round($data_persentase_tutorial, 2)."%";
  })

    //KETERANGAN UJIAN USER (MAHASISWA)
  ->addColumn('keterangan', function($keterangan)use($request){

          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG) CSL & TUTORIAL
    if ($keterangan->id_block != $request->id_block) {
      $data_jadwal_csl_tutor =Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $keterangan->id_block_mahasiswa)
      ->whereIn('penjadwalans.tipe_jadwal', ["CSL", "TUTORIAL"])->count();

      $data_user_hadir_csl_tutor = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
      ->where('presensi_mahasiswas.id_user',$keterangan->id)->where('presensi_mahasiswas.id_block',$keterangan->id_block_mahasiswa)
      ->whereIn('penjadwalans.tipe_jadwal', ["CSL", "TUTORIAL"])->count();
    }
    else{
      $data_jadwal_csl_tutor =Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $keterangan->id_block)
      ->whereIn('penjadwalans.tipe_jadwal', ["CSL", "TUTORIAL"])->count();

      $data_user_hadir_csl_tutor = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
      ->where('presensi_mahasiswas.id_user',$keterangan->id)->where('presensi_mahasiswas.id_block',$keterangan->id_block)
      ->whereIn('penjadwalans.tipe_jadwal', ["CSL", "TUTORIAL"])->count();
    }

          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG) KULIAH, PLENO & PRAKTIKUM
    if ($keterangan->id_block != $request->id_block) {
      $data_jadwal_kuliah_pleno_praktikum =Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $keterangan->id_block_mahasiswa)
      ->whereIn('penjadwalans.tipe_jadwal', ["KULIAH", "PLENO", "TUTORIAL"])->count();

      $data_user_hadir_kuliah_pleno_praktikum = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
      ->where('presensi_mahasiswas.id_user',$keterangan->id)->where('presensi_mahasiswas.id_block',$keterangan->id_block_mahasiswa)
      ->whereIn('penjadwalans.tipe_jadwal', ["KULIAH", "PLENO", "TUTORIAL"])->count();
    }
    else{
      $data_jadwal_kuliah_pleno_praktikum =Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $keterangan->id_block)
      ->whereIn('penjadwalans.tipe_jadwal', ["KULIAH", "PLENO", "TUTORIAL"])->count();

      $data_user_hadir_kuliah_pleno_praktikum = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
      ->where('presensi_mahasiswas.id_user',$keterangan->id)->where('presensi_mahasiswas.id_block',$keterangan->id_block)
      ->whereIn('penjadwalans.tipe_jadwal', ["KULIAH", "PLENO", "TUTORIAL"])->count();
    }

    if ($data_user_hadir_csl_tutor == "") {
      $data_user_hadir_csl_tutor = 0;
    };

    if ($data_jadwal_csl_tutor == "") {
      $data_jadwal_csl_tutor = 0;
    };

    if ($data_jadwal_kuliah_pleno_praktikum == "") {
      $data_jadwal_kuliah_pleno_praktikum = 0;
    };

    if ($data_user_hadir_kuliah_pleno_praktikum == "") {
      $data_user_hadir_kuliah_pleno_praktikum = 0;
    };

          //JIKA JADWAL CSL DAN TUTORNYA BELUM ADA
    if ($data_jadwal_csl_tutor == 0) {
      $data_persentase_csl_tutor = 0;
    }
    else{
      $data_persentase_csl_tutor = ($data_user_hadir_csl_tutor / $data_jadwal_csl_tutor) * 100;
    }

          //JIKA JADWAL KULIAH, PLENO DAN PRAKTIKUM BELUM ADA
    if ($data_jadwal_kuliah_pleno_praktikum == 0) {
      $data_persentase_kuliah_pleno_praktikum = 0;
    }
    else{
      $data_persentase_kuliah_pleno_praktikum = ($data_user_hadir_kuliah_pleno_praktikum / $data_jadwal_kuliah_pleno_praktikum) * 100;
    }     


    if ($data_persentase_csl_tutor >= 100 AND $data_persentase_kuliah_pleno_praktikum >= 80) {
      $data_keterangan = '<b style="color:green"> <span class="glyphicon glyphicon-ok-sign"></span> BOLEH UJIAN </b>';
    }
    else{
      $data_keterangan = '<b style="color:red"> <span class="glyphicon glyphicon-remove-sign"></span> TIDAK BOLEH UJIAN </b>';
    }

    return $data_keterangan;

  })->make(true);

}
//END CLASS LAPORAN REKAP SEMUA TIPE JADWAL

// PROSES DOWNLOAD EXCEL SEMUA TIPE JADWAL
public function download_lap_rekap_semua_presensi(Request $request, $id_block, $jenis_laporan, $tipe_jadwal, $mahasiswa, $id_kelompok) {

    //PAKAI SCOPE YG ADA DI MODEL USER
    //DOWNLOAD SEMUA TIPE JADWAL
  $data_mahasiswa = User::laporanRekapSemua($request)->get();


  Excel::create('Rekap Presensi Mahasiswa', function($excel) use ($data_mahasiswa, $request) {
          // Set property
    $excel->sheet('Rekap Presensi Mahasiswa', function($sheet) use ($data_mahasiswa, $request) {
      $row = 1;
      $sheet->row($row, [

        'NPM',
        'Nama Mahasiswa',
        'Persentase CSL',
        'Persentase KULIAH',
        'Persentase PLENO',
        'Persentase PRAKTIKUM',
        'Persentase TUTORIAL',
        'Keterangan',

      ]);


      foreach ($data_mahasiswa as $data_mahasiswas){
//PRESENTASE KEHADIRAN TIPE JADWAL

        // #CSL

          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
        if ($data_mahasiswas->id_block != $request->id_block) {
          $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $data_mahasiswas->id_block_mahasiswa)
          ->where('penjadwalans.tipe_jadwal', "CSL")->count();
        }
        else{
          $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $data_mahasiswas->id_block)
          ->where('penjadwalans.tipe_jadwal', "CSL")->count();
        }

          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
        if ($data_mahasiswas->id_block != $request->id_block) {
          $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
          ->where('presensi_mahasiswas.id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block_mahasiswa)
          ->where('penjadwalans.tipe_jadwal', "CSL")->count();
        }
        else{
          $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
          ->where('presensi_mahasiswas.id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block)
          ->where('penjadwalans.tipe_jadwal', "CSL")->count();
        }

  //JIKA JUMLAH JADWAL DAN ABSEN SAMA
        if ($data_jadwal == $data_user_hadir) {

            //JIKA JADWAL DAN ABSEN SAMA SAMA KOSONG ATAU 0 (BELUM ADA JADWAL DA ABSEN)
          if ($data_jadwal == "0" AND $data_user_hadir == "0") {
            $data_persentase_csl =  "-";
            return $data_persentase_csl;
          }
          else{
            $data_persentase_csl = 100;
          }
        }
        elseif ($data_jadwal != "" AND $data_user_hadir =="") {
          $data_persentase_csl = 0;
        }
        elseif ($data_jadwal == "" AND $data_user_hadir !="") {
          $data_persentase_csl = 0;
        }
        else{
          $data_persentase_csl = ($data_user_hadir / $data_jadwal) * 100;

          if ($data_persentase_csl > 100) {
            $data_persentase_csl = 100;
          }
        }

        // #KULIAH
          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
        if ($data_mahasiswas->id_block != $request->id_block) {
          $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $data_mahasiswas->id_block_mahasiswa)
          ->where('penjadwalans.tipe_jadwal', "KULIAH")->count();
        }
        else{
          $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $data_mahasiswas->id_block)
          ->where('penjadwalans.tipe_jadwal', "KULIAH")->count();
        }

          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
        if ($data_mahasiswas->id_block != $request->id_block) {
          $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
          ->where('presensi_mahasiswas.id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block_mahasiswa)
          ->where('penjadwalans.tipe_jadwal', "KULIAH")->count();
        }
        else{
          $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
          ->where('presensi_mahasiswas.id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block)
          ->where('penjadwalans.tipe_jadwal', "KULIAH")->count();
        }
  //JIKA JUMLAH JADWAL DAN ABSEN SAMA
        if ($data_jadwal == $data_user_hadir) {

            //JIKA JADWAL DAN ABSEN SAMA SAMA KOSONG ATAU 0 (BELUM ADA JADWAL DA ABSEN)
          if ($data_jadwal == "0" AND $data_user_hadir == "0") {
            $data_persentase_kuliah = "-";
            return $data_persentase_kuliah;
          }
          else{
            $data_persentase_kuliah = 100;
          }
        }
        elseif ($data_jadwal != "" AND $data_user_hadir =="") {
          $data_persentase_kuliah = 0;
        }
        elseif ($data_jadwal == "" AND $data_user_hadir !="") {
          $data_persentase_kuliah = 0;
        }
        else{
          $data_persentase_kuliah = ($data_user_hadir / $data_jadwal) * 100;

          if ($data_persentase_kuliah > 100) {
            $data_persentase_kuliah = 100;
          }
        }

        // #PLENO
          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
        if ($data_mahasiswas->id_block != $request->id_block) {
          $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $data_mahasiswas->id_block_mahasiswa)
          ->where('penjadwalans.tipe_jadwal', "PLENO")->count();
        }
        else{
          $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $data_mahasiswas->id_block)
          ->where('penjadwalans.tipe_jadwal', "PLENO")->count();
        }

          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
        if ($data_mahasiswas->id_block != $request->id_block) {
          $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
          ->where('presensi_mahasiswas.id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block_mahasiswa)
          ->where('penjadwalans.tipe_jadwal', "PLENO")->count();
        }
        else{
          $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
          ->where('presensi_mahasiswas.id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block)
          ->where('penjadwalans.tipe_jadwal', "PLENO")->count();
        }


  //JIKA JUMLAH JADWAL DAN ABSEN SAMA
        if ($data_jadwal == $data_user_hadir) {

            //JIKA JADWAL DAN ABSEN SAMA SAMA KOSONG ATAU 0 (BELUM ADA JADWAL DA ABSEN)
          if ($data_jadwal == "0" AND $data_user_hadir == "0") {
            $data_persentase_pleno =  "-";
            $data_persentase_pleno;
          }
          else{
            $data_persentase_pleno = 100;
          }
        }
        elseif ($data_jadwal != "" AND $data_user_hadir =="") {
          $data_persentase_pleno = 0;
        }
        elseif ($data_jadwal == "" AND $data_user_hadir !="") {
          $data_persentase_pleno = 0;
        }
        else{
          $data_persentase_pleno = ($data_user_hadir / $data_jadwal) * 100;

          if ($data_persentase_pleno > 100) {
            $data_persentase_pleno = 100;
          }
        }

        // #PRAKTIKUM
          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
        if ($data_mahasiswas->id_block != $request->id_block) {
          $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $data_mahasiswas->id_block_mahasiswa)
          ->where('penjadwalans.tipe_jadwal', "PRAKTIKUM")->count();
        }
        else{
          $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $data_mahasiswas->id_block)
          ->where('penjadwalans.tipe_jadwal', "PRAKTIKUM")->count();
        }

          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
        if ($data_mahasiswas->id_block != $request->id_block) {
          $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
          ->where('presensi_mahasiswas.id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block_mahasiswa)
          ->where('penjadwalans.tipe_jadwal', "PRAKTIKUM")->count();
        }
        else{
          $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
          ->where('presensi_mahasiswas.id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block)
          ->where('penjadwalans.tipe_jadwal', "PRAKTIKUM")->count();
        }

  //JIKA JUMLAH JADWAL DAN ABSEN SAMA
        if ($data_jadwal == $data_user_hadir) {

            //JIKA JADWAL DAN ABSEN SAMA SAMA KOSONG ATAU 0 (BELUM ADA JADWAL DA ABSEN)
          if ($data_jadwal == "0" AND $data_user_hadir == "0") {
            $data_persentase_praktikum =  "-";
            $data_persentase_praktikum;
          }
          else{
            $data_persentase_praktikum =  100;
          }
        }
        elseif ($data_jadwal != "" AND $data_user_hadir =="") {
          $data_persentase_praktikum = 0;
        }
        elseif ($data_jadwal == "" AND $data_user_hadir !="") {
          $data_persentase_praktikum = 0;
        }
        else{
          $data_persentase_praktikum = ($data_user_hadir / $data_jadwal) * 100;

          if ($data_persentase_praktikum > 100) {
            $data_persentase_praktikum = 100;
          }
        }

        // #TUTORIAL

          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
        if ($data_mahasiswas->id_block != $request->id_block) {
          $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $data_mahasiswas->id_block_mahasiswa)
          ->where('penjadwalans.tipe_jadwal', "TUTORIAL")->count();
        }
        else{
          $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $data_mahasiswas->id_block)
          ->where('penjadwalans.tipe_jadwal', "TUTORIAL")->count();
        }

          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
        if ($data_mahasiswas->id_block != $request->id_block) {
          $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
          ->where('presensi_mahasiswas.id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block_mahasiswa)
          ->where('penjadwalans.tipe_jadwal', "TUTORIAL")->count();
        }
        else{
          $data_user_hadir = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
          ->where('presensi_mahasiswas.id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block)
          ->where('penjadwalans.tipe_jadwal', "TUTORIAL")->count();
        }

  //JIKA JUMLAH JADWAL DAN ABSEN SAMA
        if ($data_jadwal == $data_user_hadir) {

            //JIKA JADWAL DAN ABSEN SAMA SAMA KOSONG ATAU 0 (BELUM ADA JADWAL DA ABSEN)
          if ($data_jadwal == "0" AND $data_user_hadir == "0") {
            $data_persentase_tutorial = "-";
            return $data_persentase_tutorial;
          }
          else{
            $data_persentase_tutorial =  100;
          }
        }
        elseif ($data_jadwal != "" AND $data_user_hadir =="") {
          $data_persentase_tutorial = 0;
        }
        elseif ($data_jadwal == "" AND $data_user_hadir !="") {
          $data_persentase_tutorial = 0;
        }
        else{
          $data_persentase_tutorial = ($data_user_hadir / $data_jadwal) * 100;

          if ($data_persentase_tutorial > 100) {
            $data_persentase_tutorial = 100;
          }
        }

    //KETERANGAN UJIAN USER (MAHASISWA)
          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG) CSL & TUTORIAL
        if ($data_mahasiswas->id_block != $request->id_block) {
          $data_jadwal_csl_tutor =Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $data_mahasiswas->id_block_mahasiswa)
          ->whereIn('penjadwalans.tipe_jadwal', ["CSL", "TUTORIAL"])->count();

          $data_user_hadir_csl_tutor = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
          ->where('presensi_mahasiswas.id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block_mahasiswa)
          ->whereIn('penjadwalans.tipe_jadwal', ["CSL", "TUTORIAL"])->count();
        }
        else{
          $data_jadwal_csl_tutor =Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $data_mahasiswas->id_block)
          ->whereIn('penjadwalans.tipe_jadwal', ["CSL", "TUTORIAL"])->count();

          $data_user_hadir_csl_tutor = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
          ->where('presensi_mahasiswas.id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block)
          ->whereIn('penjadwalans.tipe_jadwal', ["CSL", "TUTORIAL"])->count();
        }

          // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG) KULIAH, PLENO & PRAKTIKUM
        if ($data_mahasiswas->id_block != $request->id_block) {
          $data_jadwal_kuliah_pleno_praktikum =Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $data_mahasiswas->id_block_mahasiswa)
          ->whereIn('penjadwalans.tipe_jadwal', ["KULIAH", "PLENO", "TUTORIAL"])->count();

          $data_user_hadir_kuliah_pleno_praktikum = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
          ->where('presensi_mahasiswas.id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block_mahasiswa)
          ->whereIn('penjadwalans.tipe_jadwal', ["KULIAH", "PLENO", "TUTORIAL"])->count();
        }
        else{
          $data_jadwal_kuliah_pleno_praktikum =Penjadwalan::where('penjadwalans.status_jadwal','<' ,2)->where('penjadwalans.id_block', $data_mahasiswas->id_block)
          ->whereIn('penjadwalans.tipe_jadwal', ["KULIAH", "PLENO", "TUTORIAL"])->count();

          $data_user_hadir_kuliah_pleno_praktikum = PresensiMahasiswa::leftJoin('penjadwalans','presensi_mahasiswas.id_jadwal','=','penjadwalans.id')
          ->where('presensi_mahasiswas.id_user',$data_mahasiswas->id)->where('presensi_mahasiswas.id_block',$data_mahasiswas->id_block)
          ->whereIn('penjadwalans.tipe_jadwal', ["KULIAH", "PLENO", "TUTORIAL"])->count();
        }

        if ($data_user_hadir_csl_tutor == "") {
          $data_user_hadir_csl_tutor = 0;
        };

        if ($data_jadwal_csl_tutor == "") {
          $data_jadwal_csl_tutor = 0;
        };

        if ($data_jadwal_kuliah_pleno_praktikum == "") {
          $data_jadwal_kuliah_pleno_praktikum = 0;
        };

        if ($data_user_hadir_kuliah_pleno_praktikum == "") {
          $data_user_hadir_kuliah_pleno_praktikum = 0;
        };

          //JIKA JADWAL CSL DAN TUTORNYA BELUM ADA
        if ($data_jadwal_csl_tutor == 0) {
          $data_persentase_csl_tutor = 0;
        }
        else{
          $data_persentase_csl_tutor = ($data_user_hadir_csl_tutor / $data_jadwal_csl_tutor) * 100;
        }

          //JIKA JADWAL KULIAH, PLENO DAN PRAKTIKUM BELUM ADA
        if ($data_jadwal_kuliah_pleno_praktikum == 0) {
          $data_persentase_kuliah_pleno_praktikum = 0;
        }
        else{
          $data_persentase_kuliah_pleno_praktikum = ($data_user_hadir_kuliah_pleno_praktikum / $data_jadwal_kuliah_pleno_praktikum) * 100;
        }     


        if ($data_persentase_csl_tutor >= 100 AND $data_persentase_kuliah_pleno_praktikum >= 80) {
          $data_keterangan = 'BOLEH UJIAN';
        }
        else{
          $data_keterangan = 'TIDAK BOLEH UJIAN';
        }

        $sheet->row(++$row, [
          $data_mahasiswas->email,
          $data_mahasiswas->name,
          $data_persentase_csl = round($data_persentase_csl, 2)."%",
          $data_persentase_kuliah = round($data_persentase_kuliah, 2)."%",
          $data_persentase_pleno = round($data_persentase_pleno, 2)."%",
          $data_persentase_praktikum = round($data_persentase_praktikum, 2)."%",
          $data_persentase_tutorial = round($data_persentase_tutorial, 2)."%",
          $data_keterangan,
        ]); 


      }
      
    });

})->export('xls');


}
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
