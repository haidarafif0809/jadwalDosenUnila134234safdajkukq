<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Penjadwalan;
use App\PresensiMahasiswa;
use App\User;
use App\Master_block;
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
        return view('laporan_presensi_mahasiswa.index');
    }


//PROSES LAPORAN REKAP
    public function proses_laporan_rekap(Request $request){

                DB::statement(DB::raw('set @nomor=0 '));
        
                $data_angkatan = Master_block::select('id_angkatan')->where('id', $request->id_block)->first();
                $data_mahasiswa = User::select([DB::raw('@nomor := @nomor + 1 as no_urut'),'users.email AS email','users.id_angkatan AS angkatan', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
                ->leftJoin('mahasiswa_block','users.id','=','mahasiswa_block.id_mahasiswa')
                ->leftJoin('master_blocks','users.id_angkatan','=','master_blocks.id_angkatan')
                ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                ->where('role_user.role_id',3)
                ->where('master_blocks.id', $request->id_block)
                ->orwhere('mahasiswa_block.id_block', $request->id_block)->get();

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

// PROSES DOWNLOAD EXCEL
    public function download_lap_rekap_presensi(Request $request, $id_block) {

        DB::statement(DB::raw('set @nomor=0 '));

        $data_angkatan = Master_block::select('id_angkatan')->where('id', $request->id_block)->first();
        $data_mahasiswa = User::select([DB::raw('@nomor := @nomor + 1 as no_urut'),'users.email AS email', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block', DB::raw('IFNULL(mahasiswa_block.id_block, master_blocks.id) AS id_block_mahasiswa')])
                ->leftJoin('mahasiswa_block','users.id','=','mahasiswa_block.id_mahasiswa')
                ->leftJoin('master_blocks','users.id_angkatan','=','master_blocks.id_angkatan')
                ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                ->where('role_user.role_id',3)
                ->where('master_blocks.id', $request->id_block)
                ->orwhere('mahasiswa_block.id_block', $request->id_block)->get();

        Excel::create('Rekap Presensi Mahasiswa', function($excel) use ($data_mahasiswa, $request) {
          // Set property
          $excel->sheet('Rekap Presensi Mahasiswa', function($sheet) use ($data_mahasiswa, $request) {
            $row = 1;
            $sheet->row($row, [

              'No',
              'NPM',
              'Nama Mahasiswa',
              'Jumlah Jadwal',
              'Jumlah Hadir',
              'Presentase',
              'Keterangan',

            ]);

             
        foreach ($data_mahasiswa as $data_mahasiswas){

                //JUMLAH SELURUH JADWAL TERLAKSANA

                    if ($data_mahasiswas->id_block != $request->id_block) {
                      $jumlah_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)
                      ->where('penjadwalans.id_block', $data_mahasiswas->id_block_mahasiswa)->count();
                    }
                    else{
                      $jumlah_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)  
                      ->where('penjadwalans.id_block', $data_mahasiswas->id_block)->count();
                    }

                //JUMLAH JADWAL DIHADIRI USER (MAHASISWA) 
                // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
                    if ($data_mahasiswas->id_block != $request->id_block) {
                      $jumlah_hadir = PresensiMahasiswa::where('id_user',$data_mahasiswas->id)->where('id_block',$data_mahasiswas->id_block_mahasiswa)->count();
                    }
                    else{
                      $jumlah_hadir = PresensiMahasiswa::where('id_user',$data_mahasiswas->id)->where('id_block',$data_mahasiswas->id_block)->count();
                    }

                //PRESENTASE KEHADIRAN USER (MAHASISWA)
                    if ($data_mahasiswas->id_block != $request->id_block) {
                      $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)
                      ->where('penjadwalans.id_block', $data_mahasiswas->id_block_mahasiswa)->count();
                    }
                    else{
                      $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)  
                      ->where('penjadwalans.id_block', $data_mahasiswas->id_block)->count();
                    }

                    
                // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
                    if ($data_mahasiswas->id_block != $request->id_block) {
                      $data_user_hadir = PresensiMahasiswa::where('id_user',$data_mahasiswas->id)->where('id_block',$data_mahasiswas->id_block_mahasiswa)->count();
                    }
                    else{
                      $data_user_hadir = PresensiMahasiswa::where('id_user',$data_mahasiswas->id)->where('id_block',$data_mahasiswas->id_block)->count();
                    }

                        if ($data_jadwal == "" AND $data_user_hadir =="") {
                            $jumlah_presentase = 0;
                        }
                        elseif ($data_jadwal != "" AND $data_user_hadir =="") {
                            $jumlah_presentase = 0;
                        }
                        elseif ($data_jadwal == "" AND $data_user_hadir !="") {
                            $jumlah_presentase = 100;
                        }
                        else{
                            $jumlah_presentase = ($data_user_hadir / $data_jadwal) * 100;
                        }

                //KETERANGAN UJIAN USER (MAHASISWA)
                    if ($data_mahasiswas->id_block != $request->id_block) {
                      $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)
                      ->where('penjadwalans.id_block', $data_mahasiswas->id_block_mahasiswa)->count();
                    }
                    else{
                      $data_jadwal = Penjadwalan::where('penjadwalans.status_jadwal', 1)  
                      ->where('penjadwalans.id_block', $data_mahasiswas->id_block)->count();
                    }

                // JIKA ADA MAHASISWA YG BERBEDA ANGKATAN (MAHASISWA YG MENGULANG)
                    if ($data_mahasiswas->id_block != $request->id_block) {
                      $data_user_hadir = PresensiMahasiswa::where('id_user',$data_mahasiswas->id)->where('id_block',$data_mahasiswas->id_block_mahasiswa)->count();
                    }
                    else{
                      $data_user_hadir = PresensiMahasiswa::where('id_user',$data_mahasiswas->id)->where('id_block',$data_mahasiswas->id_block)->count();
                    }

                    //JIKA HASIL PRESENTASE 0
                        if ($data_jadwal == "" OR $data_user_hadir =="") {
                            $presentase = 0;
                        }
                        else{
                            $presentase = ($data_user_hadir / $data_jadwal) * 100;
                        }

                    //LOGIKA KETERNAGAN UJIAN / BOLEH UJIAN
                        if (round($presentase) >= 80 || $data_user_hadir > $data_jadwal) {
                            $keterangan = 'BOLEH UJIAN';
                        }
                        else{
                            $keterangan = 'TIDAK BOLEH UJIAN';
                        }

            $sheet->row(++$row, [
                $data_mahasiswas->no_urut,
                $data_mahasiswas->email,
                $data_mahasiswas->name,
                $jumlah_jadwal,
                $jumlah_hadir,
                $jumlah_presentase."%",
                $keterangan,
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
