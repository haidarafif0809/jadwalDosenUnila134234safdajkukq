<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Penjadwalan; 
use App\Jadwal_dosen; 

class WelcomeController extends Controller
{
    //

    public function index(Request $request, Builder $htmlBuilder)
    {
        //
        if ($request->ajax()) {
            # code...
        	$tanggal_sekarang = date('Y-m-d');// tanggal sekarang

            $penjadwalans = Penjadwalan::with(['block','mata_kuliah','ruangan','modul'])->where('tanggal',$tanggal_sekarang);
            return Datatables::of($penjadwalans)->addColumn('jadwal_dosen', function($jadwal){
                $jadwal_dosens = Jadwal_dosen::with(['jadwal','dosen'])->where('id_jadwal',$jadwal->id)->get(); 
                    return view('penjadwalans._action', [ 
                        'model_user'     => $jadwal_dosens,
                        'id_jadwal' => $jadwal->id
                        ]);
                }) 
            ->addColumn('status',function($status_penjadwalan){
                $status = "status_jadwal";
                if ($status_penjadwalan->status_jadwal == 0 ) {
                    # code...
                    $status = "Belum Terlaksana";
                }
                elseif ($status_penjadwalan->status_jadwal == 1) {
                    # code...
                     $status = "Sudah Terlaksana";
                }
                elseif ($status_penjadwalan->status_jadwal == 2) {
                    # code...
                     $status = "Batal";
                } 
                return $status;
                })->make(true);
        }
        $html = $htmlBuilder
        ->addColumn(['data' => 'tanggal', 'name' => 'tanggal', 'title' => 'Tanggal'])         
        ->addColumn(['data' => 'waktu_mulai', 'name' => 'waktu_mulai', 'title' => 'Mulai'])  
        ->addColumn(['data' => 'waktu_selesai', 'name' => 'waktu_selesai', 'title' => 'Selesai'])         
        ->addColumn(['data' => 'tipe_jadwal', 'name' => 'tipe_jadwal', 'title' => 'Tipe Jadwal'])     
        ->addColumn(['data' => 'block.nama_block', 'name' => 'block.nama_block', 'title' => 'Block', 'orderable' => false, ])
        ->addColumn(['data' => 'mata_kuliah.nama_mata_kuliah', 'name' => 'mata_kuliah.nama_mata_kuliah', 'title' => 'Mata Kuliah', 'orderable' => false, ])  
        ->addColumn(['data' => 'ruangan.nama_ruangan', 'name' => 'ruangan.nama_ruangan', 'title' => 'Ruangan', 'orderable' => false, ])    
        ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status Penjadwalan', 'orderable' => false, 'searchable'=>false])   
        ->addColumn(['data' => 'jadwal_dosen', 'name' => 'jadwal_dosen', 'title' => 'Dosen', 'orderable' => false, 'searchable'=>false]);
  
        return view('welcome')->with(compact('html'));
    }

    public function besok(Request $request, Builder $htmlBuilder)
    {
        //
        if ($request->ajax()) {
            # code...
			$besok = mktime (0,0,0, date("m"), date("d")+1,date("Y"));
        	$tanggal_besok = date('Y-m-d',$besok );// tanggal sekarang

            $penjadwalans = Penjadwalan::with(['block','mata_kuliah','ruangan','modul'])->where('tanggal',$tanggal_besok);
            return Datatables::of($penjadwalans)->addColumn('jadwal_dosen', function($jadwal){
                $jadwal_dosens = Jadwal_dosen::with(['jadwal','dosen'])->where('id_jadwal',$jadwal->id)->get(); 
                    return view('penjadwalans._action', [ 
                        'model_user'     => $jadwal_dosens,
                        'id_jadwal' => $jadwal->id
                        ]);
                }) 
            ->addColumn('status',function($status_penjadwalan){
                $status = "status_jadwal";
                if ($status_penjadwalan->status_jadwal == 0 ) {
                    # code...
                    $status = "Belum Terlaksana";
                }
                elseif ($status_penjadwalan->status_jadwal == 1) {
                    # code...
                     $status = "Sudah Terlaksana";
                }
                elseif ($status_penjadwalan->status_jadwal == 2) {
                    # code...
                     $status = "Batal";
                } 
                return $status;
                })->make(true);
        }
        $html = $htmlBuilder
        ->addColumn(['data' => 'tanggal', 'name' => 'tanggal', 'title' => 'Tanggal'])         
        ->addColumn(['data' => 'waktu_mulai', 'name' => 'waktu_mulai', 'title' => 'Mulai'])  
        ->addColumn(['data' => 'waktu_selesai', 'name' => 'waktu_selesai', 'title' => 'Selesai'])         
        ->addColumn(['data' => 'tipe_jadwal', 'name' => 'tipe_jadwal', 'title' => 'Tipe Jadwal'])     
        ->addColumn(['data' => 'block.nama_block', 'name' => 'block.nama_block', 'title' => 'Block', 'orderable' => false, ])
        ->addColumn(['data' => 'mata_kuliah.nama_mata_kuliah', 'name' => 'mata_kuliah.nama_mata_kuliah', 'title' => 'Mata Kuliah', 'orderable' => false, ])  
        ->addColumn(['data' => 'ruangan.nama_ruangan', 'name' => 'ruangan.nama_ruangan', 'title' => 'Ruangan', 'orderable' => false, ])    
        ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status Penjadwalan', 'orderable' => false, 'searchable'=>false])   
        ->addColumn(['data' => 'jadwal_dosen', 'name' => 'jadwal_dosen', 'title' => 'Dosen', 'orderable' => false, 'searchable'=>false]);
  
        return view('welcome')->with(compact('html'));
    }

    public function lusa(Request $request, Builder $htmlBuilder)
    {
        //
        if ($request->ajax()) {
            # code...
			$besok = mktime (0,0,0, date("m"), date("d")+2,date("Y"));
        	$tanggal_besok = date('Y-m-d',$besok );// tanggal sekarang

            $penjadwalans = Penjadwalan::with(['block','mata_kuliah','ruangan','modul'])->where('tanggal',$tanggal_besok);
            return Datatables::of($penjadwalans)->addColumn('jadwal_dosen', function($jadwal){
                $jadwal_dosens = Jadwal_dosen::with(['jadwal','dosen'])->where('id_jadwal',$jadwal->id)->get(); 
                    return view('penjadwalans._action', [ 
                        'model_user'     => $jadwal_dosens,
                        'id_jadwal' => $jadwal->id
                        ]);
                }) 
            ->addColumn('status',function($status_penjadwalan){
                $status = "status_jadwal";
                if ($status_penjadwalan->status_jadwal == 0 ) {
                    # code...
                    $status = "Belum Terlaksana";
                }
                elseif ($status_penjadwalan->status_jadwal == 1) {
                    # code...
                     $status = "Sudah Terlaksana";
                }
                elseif ($status_penjadwalan->status_jadwal == 2) {
                    # code...
                     $status = "Batal";
                } 
                return $status;
                })->make(true);
        }
        $html = $htmlBuilder
        ->addColumn(['data' => 'tanggal', 'name' => 'tanggal', 'title' => 'Tanggal'])         
        ->addColumn(['data' => 'waktu_mulai', 'name' => 'waktu_mulai', 'title' => 'Mulai'])  
        ->addColumn(['data' => 'waktu_selesai', 'name' => 'waktu_selesai', 'title' => 'Selesai'])         
        ->addColumn(['data' => 'tipe_jadwal', 'name' => 'tipe_jadwal', 'title' => 'Tipe Jadwal'])     
        ->addColumn(['data' => 'block.nama_block', 'name' => 'block.nama_block', 'title' => 'Block', 'orderable' => false, ])
        ->addColumn(['data' => 'mata_kuliah.nama_mata_kuliah', 'name' => 'mata_kuliah.nama_mata_kuliah', 'title' => 'Mata Kuliah', 'orderable' => false, ])  
        ->addColumn(['data' => 'ruangan.nama_ruangan', 'name' => 'ruangan.nama_ruangan', 'title' => 'Ruangan', 'orderable' => false, ])    
        ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status Penjadwalan', 'orderable' => false, 'searchable'=>false])   
        ->addColumn(['data' => 'jadwal_dosen', 'name' => 'jadwal_dosen', 'title' => 'Dosen', 'orderable' => false, 'searchable'=>false]);
  
        return view('welcome')->with(compact('html'));
    }
}
