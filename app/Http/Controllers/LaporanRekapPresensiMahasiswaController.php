<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Penjadwalan;
use App\PresensiMahasiswa;
use App\User;
use App\Master_block;
use Auth;
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


    public function proses_laporan_rekap(Request $request){

                DB::statement(DB::raw('set @nomor=0 '));
        
                $data_angkatan = Master_block::select('id_angkatan')->where('id', $request->id_block)->first();
                $data_mahasiswa = User::select([DB::raw('@nomor := @nomor + 1 as no_urut'),'users.email AS email', 'users.name AS name', 'users.id AS id', 'master_blocks.id AS id_block'])
                ->leftJoin('master_blocks','users.id_angkatan','=','master_blocks.id_angkatan')
                ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                ->where('role_user.role_id',3)
                ->where('users.id_angkatan', $data_angkatan->id_angkatan)->get();

                return Datatables::of($data_mahasiswa)

            //JUMLAH SELURUH JADWAL TERLAKSANA
                    ->addColumn('jumlah_jadwal', function($jumlah_jadwal){
                        $data_jadwal = Penjadwalan::where('id_block', $jumlah_jadwal->id_block)->where('status_jadwal', 1)->count();

                        return $data_jadwal;
                })

            //JUMLAH JADWAL DIHADIRI USER (MAHASISWA)
                    ->addColumn('jumlah_hadir', function($jumlah_hadir){
                        $data_user_hadir = PresensiMahasiswa::where('id_user',$jumlah_hadir->id)->count();

                        return $data_user_hadir;
                })

            //PRESENTASE KEHADIRAN USER (MAHASISWA)
                    ->addColumn('presentase', function($presentase){
                        $data_jadwal = Penjadwalan::where('id_block', $presentase->id_block)->where('status_jadwal', 1)->count();
                        $data_user_hadir = PresensiMahasiswa::where('id_user',$presentase->id)->count();

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
                        }

                        return round($data_presentase)."%";
                })

            //KETERANGAN UJIAN USER (MAHASISWA)
                    ->addColumn('keterangan', function($keterangan){
                        $data_jadwal = Penjadwalan::where('id_block', $keterangan->id_block)->where('status_jadwal', 1)->count();
                        $data_user_hadir = PresensiMahasiswa::where('id_user',$keterangan->id)->count();

                    //JIKA HASIL PRESENTASE 0
                        if ($data_jadwal == "" OR $data_user_hadir =="") {
                            $presentase = 0;
                        }
                        else{
                            $presentase = ($data_user_hadir / $data_jadwal) * 100;
                        }                        

                    //LOGIKA KETERNAGAN UJIAN / BOLEH UJIAN
                        if (round($presentase) >= 80 || $data_user_hadir > $data_jadwal) {
                            $data_keterangan = '<b style="color:green"> <span class="glyphicon glyphicon-ok-sign"></span> BOLEH UJIAN </b>';
                        }
                        else{
                            $data_keterangan = '<b style="color:red"> <span class="glyphicon glyphicon-remove-sign"></span> TIDAK BOLEH UJIAN </b>';
                        }

                        return $data_keterangan;
                })->make(true);
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
