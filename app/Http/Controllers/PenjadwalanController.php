<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Penjadwalan; 
use App\User; 
use App\User_otoritas; 
use App\Jadwal_dosen; 
use App\Master_ruangan;
use App\Master_block; 
use App\Master_mata_kuliah;
use App\SettingWaktu;
use App\ModulBlok;
use Session;
use Auth;
use Excel;

class PenjadwalanController extends Controller
{

    //MENAMPILKAN DATA PENJADWALAN
    public function index(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) { 
            $penjadwalans = Penjadwalan::with(['block','mata_kuliah','ruangan','modul']);
            return Datatables::of($penjadwalans)
            //MENGONEKSIKAN TOMBOL HAPUS DAN EDIT
            ->addColumn('action', function($penjadwalan){
                    return view('penjadwalans._tombol', [
                        'model'     => $penjadwalan,
                        'form_url'  => route('penjadwalans.destroy', $penjadwalan->id),
                        'edit_url'  => route('penjadwalans.edit', $penjadwalan->id),
                        'confirm_message'   => 'Apakah Anda Yakin Mau Menghapus Penjadwalan ?'
                        ]);
                })
            //MENGONEKSIKAN TOMBOL DOSEN(ISI NYA DOSEN YANG ADA DI PENJADWALAN)
            ->addColumn('jadwal_dosen', function($jadwal){
                $jadwal_dosens = Jadwal_dosen::with(['dosen'])->where('id_jadwal',$jadwal->id)->get(); 
                    return view('penjadwalans._action', [ 
                        'model_user'     => $jadwal_dosens,
                        'id_jadwal' => $jadwal->id
                        ]);
                })
            //MENGONEKSIKAN TOMBOL STATUS PENJADWALAN
            ->addColumn('tombol_status', function($data_status){  
                    return view('penjadwalans._action_status', [ 
                        'model'     => $data_status,
                        'ubah_dosen'  => route('penjadwalans.ubah_dosen', $data_status->id),
                        'terlaksana_url' => route('penjadwalans.terlaksana', $data_status->id),
                        'belum_terlaksana_url' => route('penjadwalans.belumterlaksana', $data_status->id),
                        'batal_url' => route('penjadwalans.batal', $data_status->id),
                        'terlaksana_message'   => 'Apakah Anda Yakin Penjadwalan Terlaksana ?',
                        'belum_terlaksana_message'   => 'Apakah Anda Yakin Penjadwalan Belum Terlaksana?',
                        'batal_message'   => 'Apakah Anda Yakin Mau Membatalakan Penjadwalan ?',
                        ]);
                })
            //MENAMPILKAN STATUS PENJADWALAN
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
                elseif ($status_penjadwalan->status_jadwal == 3) {
                    # code...
                     $status = "Dosen Di Gantikan";
                } 
                return $status;
                })
            ->addColumn('mata_kuliah',function($penjadwalan){

                if ($penjadwalan->id_mata_kuliah == "-" OR $penjadwalan->id_mata_kuliah == ""  OR $penjadwalan->id_mata_kuliah == "0") {
                    return "-";
                }
                else {
                    return $penjadwalan->mata_kuliah->nama_mata_kuliah;
                }
            })->make(true);
        }

        //MENAMPILKAN COLUM PENJADWALAN
        $html = $htmlBuilder
        ->addColumn(['data' => 'tanggal', 'name' => 'tanggal', 'title' => 'Tanggal'])         
        ->addColumn(['data' => 'waktu_mulai', 'name' => 'waktu_mulai', 'title' => 'Mulai'])  
        ->addColumn(['data' => 'waktu_selesai', 'name' => 'waktu_selesai', 'title' => 'Selesai'])         
        ->addColumn(['data' => 'tipe_jadwal', 'name' => 'tipe_jadwal', 'title' => 'Tipe Jadwal'])     
        ->addColumn(['data' => 'block.nama_block', 'name' => 'block.nama_block', 'title' => 'Block', 'orderable' => false, ])
        ->addColumn(['data' => 'mata_kuliah', 'name' => 'mata_kuliah', 'title' => 'Mata Kuliah', 'orderable' => false, ])  
        ->addColumn(['data' => 'ruangan.nama_ruangan', 'name' => 'ruangan.nama_ruangan', 'title' => 'Ruangan', 'orderable' => false, ])    
        ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status', 'orderable' => false, 'searchable'=>false])    
        ->addColumn(['data' => 'tombol_status', 'name' => 'tombol_status', 'title' => '', 'orderable' => false, 'searchable'=>false])   
        ->addColumn(['data' => 'jadwal_dosen', 'name' => 'jadwal_dosen', 'title' => 'Dosen', 'orderable' => false, 'searchable'=>false])     
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Ubah & Hapus', 'orderable' => false, 'searchable'=>false]);

        //MENAMPILKAN DOSEN DI FILTER
          $users = DB::table('users')
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id',2)
            ->pluck('name','id');

           $users->prepend('Semua Dosen', 'semua');

        return view('penjadwalans.index',['users'=> $users])->with(compact('html'));
    }

public function exportPost(Request $request, Builder $htmlBuilder) {  
        $this->validate($request, [
     
            'dari_tanggal'     => 'required',
            'sampai_tanggal'     => 'required',
            'id_ruangan'    => 'required',
            'id_dosen'    => 'required'
            ]);   

            if ($request->id_ruangan == 'semua' && $request->id_dosen == 'semua' && $request->id_block == 'semua') {
                
                $penjadwalans = Penjadwalan::with(['block','mata_kuliah','ruangan'])->where('tanggal' ,'>=',$request->dari_tanggal)->where('tanggal','<=',$request->sampai_tanggal)->get();

                $jenis_id_jadwal = 1;
      

            }
             elseif ($request->id_ruangan != 'semua' && $request->id_dosen != 'semua' && $request->id_block != 'semua') {

                 $penjadwalans = Jadwal_dosen::with(['block','mata_kuliah','ruangan'])->where('id_ruangan',$request->id_ruangan)->where('id_dosen',$request->id_dosen)->where('id_block',$request->id_block)->where('tanggal' ,'>=',$request->dari_tanggal)->where('tanggal','<=',$request->sampai_tanggal)->groupBy('id_jadwal')->get();
                 
                 $jenis_id_jadwal = 0;
                
            }

            elseif ($request->id_ruangan == 'semua' && $request->id_dosen != 'semua' && $request->id_block == 'semua' ) {

                 $penjadwalans = Jadwal_dosen::with(['block','mata_kuliah','ruangan'])->where('id_dosen',$request->id_dosen)->where('tanggal' ,'>=',$request->dari_tanggal)->where('tanggal','<=',$request->sampai_tanggal)->get();

                 $jenis_id_jadwal = 0;      

            }
            elseif ($request->id_ruangan != 'semua' && $request->id_dosen == 'semua' && $request->id_block == 'semua') {

                 $penjadwalans = Jadwal_dosen::with(['block','mata_kuliah','ruangan'])->where('id_ruangan',$request->id_ruangan)->where('tanggal' ,'>=',$request->dari_tanggal)->where('tanggal','<=',$request->sampai_tanggal)->groupBy('id_jadwal')->get();
                 $jenis_id_jadwal = 0;

            } 
             elseif ($request->id_ruangan == 'semua' && $request->id_dosen == 'semua' && $request->id_block != 'semua') {

                 $penjadwalans = Jadwal_dosen::with(['block','mata_kuliah','ruangan'])->where('id_block',$request->id_block)->where('tanggal' ,'>=',$request->dari_tanggal)->where('tanggal','<=',$request->sampai_tanggal)->groupBy('id_jadwal')->get();
                 $jenis_id_jadwal = 0;

            } 
            elseif ($request->id_ruangan == 'semua' && $request->id_dosen != 'semua' && $request->id_block != 'semua') {

                 $penjadwalans = Jadwal_dosen::with(['block','mata_kuliah','ruangan'])->where('id_dosen',$request->id_dosen)->where('id_block',$request->id_block)->where('tanggal' ,'>=',$request->dari_tanggal)->where('tanggal','<=',$request->sampai_tanggal)->groupBy('id_jadwal')->get();
                 $jenis_id_jadwal = 0;

            } elseif ($request->id_ruangan != 'semua' && $request->id_dosen == 'semua' && $request->id_block != 'semua') {

                 $penjadwalans = Jadwal_dosen::with(['block','mata_kuliah','ruangan'])->where('id_ruangan',$request->id_ruangan)->where('id_block',$request->id_block)->where('tanggal' ,'>=',$request->dari_tanggal)->where('tanggal','<=',$request->sampai_tanggal)->groupBy('id_jadwal')->get();
                 $jenis_id_jadwal = 0;

            } elseif ($request->id_ruangan != 'semua' && $request->id_dosen != 'semua' && $request->id_block == 'semua') {

                 $penjadwalans = Jadwal_dosen::with(['block','mata_kuliah','ruangan'])->where('id_ruangan',$request->id_ruangan)->where('id_dosen',$request->id_dosen)->where('tanggal' ,'>=',$request->dari_tanggal)->where('tanggal','<=',$request->sampai_tanggal)->groupBy('id_jadwal')->get();
                 $jenis_id_jadwal = 0;

            }



    Excel::create('Data Penjadwalan', function($excel) use ($penjadwalans , $jenis_id_jadwal) {
      // Set property        
      $excel->sheet('Data Penjadwalan', function($sheet) use ($penjadwalans,$jenis_id_jadwal) {
        $row = 1;
        $sheet->row($row, [

          'Tanggal',
          'Mulai',
          'Selesai',
          'Block',
          'Mata Kuliah',
          'Ruangan',
          'Dosen',
          'Status Jadwal'

        ]);

             
        foreach ($penjadwalans as $penjadwalan) {

               if ($jenis_id_jadwal == 1) {
                   $id_jadwal = $penjadwalan->id;
                }
                else {
                     $id_jadwal = $penjadwalan->id_jadwal;
                }

                if ($penjadwalan->status_jadwal == 0 ) {
                    # code...
                    $status = "Belum Terlaksana";
                }
                elseif ($penjadwalan->status_jadwal == 1) {
                    # code...
                     $status = "Sudah Terlaksana";
                }
                elseif ($penjadwalan->status_jadwal == 2) {
                    # code...
                     $status = "Batal";
                } 
                elseif ($status_penjadwalan->status_jadwal == 3) {
                    # code...
                     $status = "Dosen Di Gantikan";
                } 

                 $jadwal_dosens = Jadwal_dosen::with(['dosen'])->where('id_jadwal',$id_jadwal)->get(); 

                 $dosen_list = "";

                 $no_urut = 0;
                foreach ($jadwal_dosens as $jadwal_dosen) {
                    $no_urut++;
                    if ($no_urut == 1){
                    $dosen_list.= $jadwal_dosen->dosen->name;
                    }
                    else{
                    $dosen_list.= ",".$jadwal_dosen->dosen->name;
                    }
                }
            if ($penjadwalan->id_mata_kuliah == "-") {
                
                $mata_kuliah = "-";
            }
            else {
                $mata_kuliah =$penjadwalan->mata_kuliah->nama_mata_kuliah;
            }


             $sheet->row(++$row, [
            $penjadwalan->tanggal,
            $penjadwalan->waktu_mulai,
            $penjadwalan->waktu_selesai,
            $penjadwalan->block->nama_block,
            $mata_kuliah,
            $penjadwalan->ruangan->nama_ruangan,
            $dosen_list,
            $status,
            ]); 


      }
      
      });

    })->export('xls');



            $users = DB::table('users')
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id',2)
            ->pluck('name','id');

           $users->prepend('Semua Dosen', 'semua');

        return view('penjadwalans.index',['users'=> $users])->with(compact('html'));

    
}

public function filter(Request $request, Builder $htmlBuilder)
    {
        // 
        $this->validate($request, [
     
            'dari_tanggal'     => 'required',
            'sampai_tanggal'     => 'required',
            'id_ruangan'    => 'required',
            'id_dosen'    => 'required'
            ]);   

         if ($request->ajax()) {
            # code...

            if ($request->id_ruangan == 'semua' && $request->id_dosen == 'semua' && $request->id_block == 'semua') {
                
                $penjadwalans = Penjadwalan::with(['block','mata_kuliah','ruangan'])->where('tanggal' ,'>=',$request->dari_tanggal)->where('tanggal','<=',$request->sampai_tanggal);

                $jenis_id_jadwal = 1;
      

            }
             elseif ($request->id_ruangan != 'semua' && $request->id_dosen != 'semua' && $request->id_block != 'semua') {

                 $penjadwalans = Jadwal_dosen::with(['block','mata_kuliah','ruangan'])->where('id_ruangan',$request->id_ruangan)->where('id_dosen',$request->id_dosen)->where('id_block',$request->id_block)->where('tanggal' ,'>=',$request->dari_tanggal)->where('tanggal','<=',$request->sampai_tanggal)->groupBy('id_jadwal');
                 
                 $jenis_id_jadwal = 0;
                
            }

            elseif ($request->id_ruangan == 'semua' && $request->id_dosen != 'semua' && $request->id_block == 'semua' ) {

                 $penjadwalans = Jadwal_dosen::with(['block','mata_kuliah','ruangan'])->where('id_dosen',$request->id_dosen)->where('tanggal' ,'>=',$request->dari_tanggal)->where('tanggal','<=',$request->sampai_tanggal);

                 $jenis_id_jadwal = 0;      

            }
            elseif ($request->id_ruangan != 'semua' && $request->id_dosen == 'semua' && $request->id_block == 'semua') {

                 $penjadwalans = Jadwal_dosen::with(['block','mata_kuliah','ruangan'])->where('id_ruangan',$request->id_ruangan)->where('tanggal' ,'>=',$request->dari_tanggal)->where('tanggal','<=',$request->sampai_tanggal)->groupBy('id_jadwal');
                 $jenis_id_jadwal = 0;

            } 
             elseif ($request->id_ruangan == 'semua' && $request->id_dosen == 'semua' && $request->id_block != 'semua') {

                 $penjadwalans = Jadwal_dosen::with(['block','mata_kuliah','ruangan'])->where('id_block',$request->id_block)->where('tanggal' ,'>=',$request->dari_tanggal)->where('tanggal','<=',$request->sampai_tanggal)->groupBy('id_jadwal');
                 $jenis_id_jadwal = 0;

            } 
            elseif ($request->id_ruangan == 'semua' && $request->id_dosen != 'semua' && $request->id_block != 'semua') {

                 $penjadwalans = Jadwal_dosen::with(['block','mata_kuliah','ruangan'])->where('id_dosen',$request->id_dosen)->where('id_block',$request->id_block)->where('tanggal' ,'>=',$request->dari_tanggal)->where('tanggal','<=',$request->sampai_tanggal)->groupBy('id_jadwal');
                 $jenis_id_jadwal = 0;

            } elseif ($request->id_ruangan != 'semua' && $request->id_dosen == 'semua' && $request->id_block != 'semua') {

                 $penjadwalans = Jadwal_dosen::with(['block','mata_kuliah','ruangan'])->where('id_ruangan',$request->id_ruangan)->where('id_block',$request->id_block)->where('tanggal' ,'>=',$request->dari_tanggal)->where('tanggal','<=',$request->sampai_tanggal)->groupBy('id_jadwal');
                 $jenis_id_jadwal = 0;

            } elseif ($request->id_ruangan != 'semua' && $request->id_dosen != 'semua' && $request->id_block == 'semua') {

                 $penjadwalans = Jadwal_dosen::with(['block','mata_kuliah','ruangan'])->where('id_ruangan',$request->id_ruangan)->where('id_dosen',$request->id_dosen)->where('tanggal' ,'>=',$request->dari_tanggal)->where('tanggal','<=',$request->sampai_tanggal)->groupBy('id_jadwal');
                 $jenis_id_jadwal = 0;

            } 
           
     
            return Datatables::of($penjadwalans)->addColumn('action', function($penjadwalan) use ($jenis_id_jadwal) {
                        
                        if ($jenis_id_jadwal == 1) {
                            
                            $id_jadwal = $penjadwalan->id;
                        }
                        else {
                            $id_jadwal = $penjadwalan->id_jadwal;
                        }

                    return view('datatable._action', [
                        'model'     => $penjadwalan,
                        'form_url'  => route('penjadwalans.destroy', $id_jadwal),
                        'edit_url'  => route('penjadwalans.edit', $id_jadwal),
                        'confirm_message'   => 'Yakin Mau Menghapus Jadwal ?'
                        ]);
                })
            ->addColumn('jadwal_dosen', function($penjadwalan) use ($jenis_id_jadwal) {
                   if ($jenis_id_jadwal == 0) {

                        $id_jadwal = $penjadwalan->id_jadwal;  
                   

                    
                    } else 
                    {
                        $id_jadwal = $penjadwalan->id;
                    }

                $jadwal_dosens = Jadwal_dosen::with(['dosen'])->where('id_jadwal',$id_jadwal)->get(); 
                    return view('penjadwalans._action', [ 
                        'model_user'     => $jadwal_dosens,
                        'id_jadwal' => $id_jadwal
                    ]);
                })
             //MENAMPILKAN STATUS PENJADWALAN
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
                elseif ($status_penjadwalan->status_jadwal == 3) {
                    # code...
                     $status = "Dosen Di Gantikan";
                } 
                return $status;
                })
             ->addColumn('tombol_status', function($data_status){  
                    return view('penjadwalans._action_status', [ 
                        'model'     => $data_status,
                        'ubah_dosen'  => route('penjadwalans.ubah_dosen', $data_status->id),
                        'terlaksana_url' => route('penjadwalans.terlaksana', $data_status->id),
                        'belum_terlaksana_url' => route('penjadwalans.belumterlaksana', $data_status->id),
                        'batal_url' => route('penjadwalans.batal', $data_status->id),
                        'terlaksana_message'   => 'Apakah Anda Yakin Penjadwalan Terlaksana ?',
                        'belum_terlaksana_message'   => 'Apakah Anda Yakin Penjadwalan Belum Terlaksana?',
                        'batal_message'   => 'Apakah Anda Yakin Mau Membatalakan Penjadwalan ?',
                        ]);
                })
            ->addColumn('mata_kuliah',function($penjadwalan){

                if ($penjadwalan->id_mata_kuliah == "-" OR $penjadwalan->id_mata_kuliah == ""  OR $penjadwalan->id_mata_kuliah == "0") {
                    return "-";
                }
                else {
                    return $penjadwalan->mata_kuliah->nama_mata_kuliah;
                }
            })->make(true);
        }
        $html = $htmlBuilder
        ->addColumn(['data' => 'tanggal', 'name' => 'tanggal', 'title' => 'Tanggal'])         
        ->addColumn(['data' => 'waktu_mulai', 'name' => 'waktu_mulai', 'title' => 'Mulai'])  
        ->addColumn(['data' => 'waktu_selesai', 'name' => 'waktu_selesai', 'title' => 'Selesai'])         
        ->addColumn(['data' => 'tipe_jadwal', 'name' => 'tipe_jadwal', 'title' => 'Tipe Jadwal'])     
        ->addColumn(['data' => 'block.nama_block', 'name' => 'block.nama_block', 'title' => 'Block', 'orderable' => false, ])
        ->addColumn(['data' => 'mata_kuliah', 'name' => 'mata_kuliah', 'title' => 'Mata Kuliah', 'orderable' => false, ])  
        ->addColumn(['data' => 'ruangan.nama_ruangan', 'name' => 'ruangan.nama_ruangan', 'title' => 'Ruangan', 'orderable' => false, ])    
        ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status', 'orderable' => false, 'searchable'=>false])    
        ->addColumn(['data' => 'tombol_status', 'name' => 'tombol_status', 'title' => '', 'orderable' => false, 'searchable'=>false])   
        ->addColumn(['data' => 'jadwal_dosen', 'name' => 'jadwal_dosen', 'title' => 'Dosen', 'orderable' => false, 'searchable'=>false])     
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Ubah & Hapus', 'orderable' => false, 'searchable'=>false]);

            $users = DB::table('users')
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id',2)
            ->pluck('name','id');

           $users->prepend('Semua Dosen', 'semua');

        return view('penjadwalans.index',['users'=> $users])->with(compact('html'));
  
    }

//PROSES KE HALAMAN TAMBAH PENJADWALANA
    public function create()
    { 
        //MENAMPILKAN USER YANG OTORITAS NYA DOSEN
        $users = DB::table('users')
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id',2)
            ->pluck('name','id'); 

        //MENAMPILKAN BLOCK SESUAI DENGAN YANG LOGIN(APABILA PJ DOSEN YANG LOGIN MAKA BLOCK NYA YANG MUNCUL USER PJ DOSEN YANG ADA DI BLOK DAN APABILA ADMIN MAKA MUNCUL SEMUA BLOCK)
        $pj_dosen = Auth::user()->id;
        $data_block = DB::table('master_blocks')
            ->leftJoin('user_pj_dosens', 'master_blocks.id', '=', 'user_pj_dosens.id_master_block')
            ->where('user_pj_dosens.id_pj_dosen',$pj_dosen)
            ->pluck('master_blocks.nama_block','master_blocks.id'); 
 
        return view('penjadwalans.create',['users' => $users,'data_block' => $data_block]);
    }

    //MENAMPILKAN MODUL DENGAN BLOCK YANG DI PILIH
    public function data_modul_perblock_penjadwalan (Request $request){
        if ($request->ajax()) {
            
            $modul =  ModulBlok::with('modul')->where('id_blok',$request->id_block)->orderBy('dari_tanggal','ASC')->get();
                echo "<option readonly='on'>Pilih Modul</option>";
            foreach ($modul as $data) {
                echo "<option value='".$data->id_modul_blok."'>".$data->modul->nama_modul."</option>";
            }

        }
    }

    //MENAMPILKAN TANGGAL SESUAI PERIODE YANG ADA DI MODUL
    public function tanggal_modul_perblock_penjadwalan (Request $request){
        if ($request->ajax()) {
            
            $modul =  ModulBlok::select('id_modul','dari_tanggal','sampai_tanggal')->where('id_modul_blok',$request->id_modul)->first(); 
           return $modul->dari_tanggal.','.$modul->sampai_tanggal;
        }
    }
 
    //PROSES MEMBUAT PENJADWALAN
    public function store(Request $request)
    { 
        $this->validate($request, [
            'tanggal'   => 'required',
            'data_waktu'     => 'required', 
            'id_block'    => 'required|exists:master_blocks,id',
            'id_ruangan'    => 'required|exists:master_ruangans,id',
            'id_user'    => 'required|exists:users,id',
            'modul'    => 'required',
            'tipe_jadwal'    => 'required',
        ]);   

        //jika tipe jadwalnya bukan kuliah atau praktikum maka mata kuliah nya -
        if ($request->id_mata_kuliah == NULL) {
            $request->id_mata_kuliah = "-";
        }

        //MEMISAHKAN WAKTU MULAI DAN SELESAIA
        $data_setting_waktu = explode("-",$request->data_waktu);
  
        //MENGECEK PENJADWLAN
        $data_penjadwalan = Penjadwalan::statusRuangan($request,$data_setting_waktu);  

        //APABILA $data_penjadwalan == 0 maka ngecek dosen
        if ($data_penjadwalan->count() == 0) { 
            //MENGECEK DOSEN DI JADWALAN YANG SAMA
            $dosen_punya_jadwal = array();
                foreach ($request->id_user as $user_dosen) {
                 $jadwal_dosen = Jadwal_dosen::statusDosen($request,$user_dosen,$data_setting_waktu); 
                 $data_jadwal_dosen = $jadwal_dosen->first(); 

                if ($jadwal_dosen->count() > 0) {
                    array_push($dosen_punya_jadwal, ['id_jadwal'=>$data_jadwal_dosen->id_jadwal,'id_dosen'=>$data_jadwal_dosen->id_dosen]);
                }
            } 
            //APABILA JADWAL NYA SAMA MAKA MUNCUL PERINGATAN
                if (count($dosen_punya_jadwal) > 0 ) { 
                    $message = 'Tidak Bisa Menambahkan Dosen Berikut Karena Sudah Memiliki Jadwal :<ul>'; 

                     foreach ($dosen_punya_jadwal as $dosen_punya_jadwals) {  
                            $nama_dosen = User::find($dosen_punya_jadwals['id_dosen']);
                            $data_penjadwalans = Penjadwalan::find($dosen_punya_jadwals['id_jadwal']); 

                            if ($data_penjadwalans->id_mata_kuliah == NULL OR $data_penjadwalans->id_mata_kuliah == '-') {
                            $message .= "<li><b>$nama_dosen->name</b> Memilik Jadwal Di Ruangan <b>".$data_penjadwalans->ruangan->nama_ruangan." </b> Block <b>".$data_penjadwalans->block->nama_block."</b></li>";
                            }else{
                            $message .= "<li><b>$nama_dosen->name</b> Memilik Jadwal Di Ruangan <b>".$data_penjadwalans->ruangan->nama_ruangan."</b> Block <b>".$data_penjadwalans->block->nama_block."</b>  Mata Kuliah <b>". $data_penjadwalans->mata_kuliah->nama_mata_kuliah." </b> </li>";   
                            }
                        }
                    $message .= '</ul>';

                    Session::flash("flash_notification", [
                        "level"=>"danger",
                        "message"=>"$message"
                        ]); 
                    return redirect()->back()->withInput();
                }
        }
        else{
            //APABILA RUANGAN SUDAH DI PAKAI DI WAKTU YANG BERSAMAAN MAKA MUNCUL ALERT DI BAWAH
            $data_ruangan =  Master_ruangan::find($request->id_ruangan);
            $data_block = Master_block::find($request->id_block);
            $data_mata_kuliah = Master_mata_kuliah::find($data_penjadwalan->first()->id_mata_kuliah); 
       
                if ($data_penjadwalan->first()->id_mata_kuliah == NULL OR $data_penjadwalan->first()->id_mata_kuliah == '-') { 
                    Session::flash("flash_notification", [
                        "level"=>"danger",
                        "message"=>"Ruangan $data_ruangan->nama_ruangan Sudah Di Pakai Block $data_block->nama_block"
                        ]);
                }else{
                    Session::flash("flash_notification", [
                        "level"=>"danger",
                        "message"=>"Ruangan $data_ruangan->nama_ruangan Sudah Di Pakai Block $data_block->nama_block Mata Kuliah $data_mata_kuliah->nama_mata_kuliah"
                        ]);
                }
            return redirect()->back()->withInput();
        } 

     
        //JIKA PENJADWALAN TIDAK ADA YANG SAMA MAKA PROSES TAMBAH PENJADWALAN BERHASIL

        $penjadwalan = Penjadwalan::create([ 
            'tanggal' =>$request->tanggal,
            'waktu_mulai'=>$data_setting_waktu[0],
            'waktu_selesai'=>$data_setting_waktu[1],
            'id_block'=>$request->id_block,
            'id_modul'=>$request->modul,
            'tipe_jadwal'=>$request->tipe_jadwal,
            'id_mata_kuliah'=>$request->id_mata_kuliah,
            'id_ruangan'=>$request->id_ruangan]);

        //UNTUK MEMBUAT JADWAL DOSEN YANG BERKAIT SAMA PENJADWALAN
            foreach ($request->id_user as $user_dosen) { 
                $jadwal_dosen = Jadwal_dosen::create([ 
                    'id_jadwal' =>$penjadwalan->id,
                    'id_dosen'=>$user_dosen,
                    'id_block'=>$request->id_block,
                    'id_mata_kuliah'=>$request->id_mata_kuliah,
                    'id_ruangan'=>$request->id_ruangan,
                    'tanggal' =>$request->tanggal,
                    'waktu_mulai'=>$data_setting_waktu[0],
                    'waktu_selesai'=>$data_setting_waktu[1],
                    ]);                
            }
        //ALERT JIKA BERHASIL
        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Berhasil Menambah Penjadwalan"
            ]);

        //APABILA TAMBAH DI BLOCK->MODUL->JADWAL
        if (isset($request->asal_input)) {
            # code...
            return redirect()->back();
        }//APABILA TAMBAH DI PENJADWALAN->TAMBAH PENJADWALAN
        else {
            return redirect()->route('penjadwalans.index'); 
        }
       
    }


       public function status_terlaksana($id){ 
        
        //MEMBUAT PROSES UPDATE STATUS TERLAKSANA PENJADWALAN DAN JADWAL DOSEN
            $penjadwalan = Penjadwalan::find($id)->update(["status_jadwal" => 1]);
            $jadwal_dosen = Jadwal_dosen::where("id_jadwal",$id)->update(["status_jadwal" => 1]);

        Session::flash("flash_notification", [
            "level"=>"info",
            "message"=>"Penjadwalan Berhasil Terlaksana"
        ]);
 
        return redirect()->route('penjadwalans.index');
    } 


    public function status_belum_terlaksana($id){ 

        //MEMBUAT PROSES UPDATE STATUS BELUM TERLAKSANA PENJADWALAN DAN JADWAL DOSEN
            $penjadwalan = Penjadwalan::find($id)->update(["status_jadwal" => 0]);
            $jadwal_dosen = Jadwal_dosen::where("id_jadwal",$id)->update(["status_jadwal" => 0]);

        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Penjadwalan Berhasil Belum Terlaksana"
        ]);
 
        return redirect()->route('penjadwalans.index');
    } 

    public function status_batal($id){ 

        //MEMBUAT PROSES UPDATE STATUS BATAL PENJADWALAN DAN JADWAL DOSEN
            $penjadwalan = Penjadwalan::find($id)->update(["status_jadwal" => 2]);
            $jadwal_dosen = Jadwal_dosen::where("id_jadwal",$id)->update(["status_jadwal" => 2]);

        Session::flash("flash_notification", [
            "level"=>"danger",
            "message"=>"Penjadwalan Berhasil Di Batalkan"
        ]);
 
        return redirect()->route('penjadwalans.index');
    }  
     public function status_batal_dosen(Request $request){ 

        //MEMBUAT PROSES UPDATE STATUS BATAL PENJADWALAN DAN JADWAL DOSEN
            $penjadwalan = Penjadwalan::find($id)->update(["status_jadwal" => 2]);
            $jadwal_dosen = Jadwal_dosen::where("id_jadwal",$request->id_jadwal)->update(["status_jadwal" => 2]);
 
        Session::flash("flash_notification", [
            "level"=>"danger",
            "message"=>"Jadwal Berhasil Di Batalkan"
        ]);
 
        return redirect()->back();
    } 

    public function status_ubah_dosen($id)
    {
        //MENYIAPKAN DATA UNTUNG DI TAMPILKAN DI EDIT PENJDWALAN
        $penjadwalans = Penjadwalan::find($id);
        //MENAMPILKAN USER YANG OTORITAS DOSEN
        $users = DB::table('users')
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id',2)
            ->pluck('name','id');

        //MENAMPILKAN BLOCK SESUAI DENGAN YANG LOGIN(APABILA PJ DOSEN YANG LOGIN MAKA BLOCK NYA YANG MUNCUL USER PJ DOSEN YANG ADA DI BLOK DAN APABILA ADMIN MAKA MUNCUL SEMUA BLOCK)
        $pj_dosen = Auth::user()->id;
        $data_block = DB::table('master_blocks')
            ->leftJoin('user_pj_dosens', 'master_blocks.id', '=', 'user_pj_dosens.id_master_block')
            ->where('user_pj_dosens.id_pj_dosen',$pj_dosen)
            ->pluck('master_blocks.nama_block','master_blocks.id'); 
        //MENAMPILKAN JADWAL DOSEN SESUAI YANG ADA DI PENJADWALAN
        $jadwal_dosen = DB::table('jadwal_dosens')
            ->leftJoin('users', 'users.id', '=', 'jadwal_dosens.id_dosen')
            ->where('jadwal_dosens.id_jadwal',$id)
            ->pluck('users.name','users.id'); 
            
            $dosen = Jadwal_dosen::with(['jadwal','dosen'])->where('id_jadwal',$id)->get(); 
            $data_dosen = '';
            foreach ($dosen as $dosens) { 
              $data_dosen .= ( "'".$dosens->dosen->id ."'," ); //untuk menampilkan data user yang sesuai ketika tambah
            }    
        //MENAMPILKAN WAKTU SUSAI YANG ADA DI PENJADWALAN
         $data_waktu = substr($penjadwalans->waktu_mulai, 0, -3) ." - ".substr($penjadwalans->waktu_selesai, 0, -3);

        //MENAMPILKAN MODUL BLOK YANG ADA DI PENJADWALAN
        $modul = ModulBlok::leftJoin('moduls','moduls.id','=','modul_bloks.id_modul')->where('id_blok',$penjadwalans->id_block)->pluck('moduls.nama_modul','modul_bloks.id_modul_blok');
      
        return view('penjadwalans.ubah_dosen',['users' => $users,'data_waktu' => $data_waktu,'data_block'=>$data_block])->with(compact('penjadwalans','data_dosen','modul')); 
    }

    //PROSES UPDATE PENJADWALAN
    public function proses_ubah_dosen(Request $request, $id)
    {
        //NGAMBIL DATA DARI FORM EDIT
         $this->validate($request, [
            'tanggal'   => 'required',
            'data_waktu'     => 'required',
            'id_block'    => 'required|exists:master_blocks,id',
            'id_ruangan'    => 'required|exists:master_ruangans,id',
            'id_user'    => 'required|exists:users,id',
            'modul'    => 'required',
            'tipe_jadwal'    => 'required',
            ]); 

          //jika tipe jadwalnya bukan kuliah atau praktikum maka mata kuliah nya -
        if ($request->id_mata_kuliah == NULL) {
            $request->id_mata_kuliah = "-";
        }

         //MEMISAHKAN WAKTU MULAI DAN SELESAI
        $data_setting_waktu = explode("-",$request->data_waktu);
            //MENGECEK DATA YANG SAMA APA TIDAK 
            $data_penjadwalan = Penjadwalan::statusRuanganEdit($request,$data_setting_waktu,$id); 
            //APABILA $data_penjadwalan == 0 maka ngecek dosen
            if ($data_penjadwalan->count() == 0) {
                $dosen_punya_jadwal = array();
                foreach ($request->id_user as $user_dosen) {
                 $jadwal_dosen = Jadwal_dosen::statusDosenEdit($request,$user_dosen,$data_setting_waktu,$id); 
                 $data_jadwal_dosen = $jadwal_dosen->first(); 

                    if ($jadwal_dosen->count() > 0) {
                        array_push($dosen_punya_jadwal, ['id_jadwal'=>$data_jadwal_dosen->id_jadwal,'id_dosen'=>$data_jadwal_dosen->id_dosen]);
                    }
                } 
            //APABILA JADWAL NYA SAMA MAKA MUNCUL PERINGATAN
                if (count($dosen_punya_jadwal) > 0 ) { 
                    $message = 'Tidak Bisa Menambahkan Dosen Berikut Karena Sudah Memiliki Jadwal :<ul>'; 
                        foreach ($dosen_punya_jadwal as $dosen_punya_jadwals) {  
                            $nama_dosen = User::find($dosen_punya_jadwals['id_dosen']);
                            $data_penjadwalans = Penjadwalan::find($dosen_punya_jadwals['id_jadwal']); 

                            if ($data_penjadwalans->id_mata_kuliah == NULL OR $data_penjadwalans->id_mata_kuliah == '-') {
                            $message .= "<li><b>$nama_dosen->name</b> Memilik Jadwal Di Ruangan <b>".$data_penjadwalans->ruangan->nama_ruangan." </b> Block <b>".$data_penjadwalans->block->nama_block."</b></li>";
                            }else{
                            $message .= "<li><b>$nama_dosen->name</b> Memilik Jadwal Di Ruangan <b>".$data_penjadwalans->ruangan->nama_ruangan."</b> Block <b>".$data_penjadwalans->block->nama_block."</b>  Mata Kuliah <b>". $data_penjadwalans->mata_kuliah->nama_mata_kuliah." </b> </li>";   
                            }
                        }
                    $message .= '</ul>';

                    Session::flash("flash_notification", [
                        "level"=>"danger",
                        "message"=>"$message"
                        ]); 
                    return redirect()->back()->withInput();
                }
            }
            else{ 
            //APABILA RUANGAN SUDAH DI PAKAI MAKA MUNCUL PERINGATAN 
                $data_ruangan =  Master_ruangan::find($request->id_ruangan);
                $data_block = Master_block::find($request->id_block);
                $data_mata_kuliah = Master_mata_kuliah::find($data_penjadwalan->first()->id_mata_kuliah);
   
                if ($data_penjadwalan->first()->id_mata_kuliah == NULL OR $data_penjadwalan->first()->id_mata_kuliah == '-') { 
                    Session::flash("flash_notification", [
                        "level"=>"danger",
                        "message"=>"Ruangan $data_ruangan->nama_ruangan Sudah Di Pakai Block $data_block->nama_block"
                        ]);
                }else{
                    Session::flash("flash_notification", [
                        "level"=>"danger",
                        "message"=>"Ruangan $data_ruangan->nama_ruangan Sudah Di Pakai Block $data_block->nama_block Mata Kuliah $data_mata_kuliah->nama_mata_kuliah"
                        ]);
                }
                return redirect()->back()->withInput();
            } 
       
        //JIKA PENJADWALAN TIDAK ADA YANG SAMA MAKA PROSES TAMBAH PENJADWALAN BERHASIL 
            $penjadwalan = Penjadwalan::find($id)->update(["status_jadwal" => 3]);
            $jadwal_dosen = Jadwal_dosen::where("id_jadwal",$id)->update(["status_jadwal" => 3]);

        //JIKA PENJADWALAN TIDAK ADA YANG SAMA MAKA PROSES TAMBAH PENJADWALAN BERHASIL

        $penjadwalan = Penjadwalan::create([ 
            'tanggal' =>$request->tanggal,
            'waktu_mulai'=>$data_setting_waktu[0],
            'waktu_selesai'=>$data_setting_waktu[1],
            'id_block'=>$request->id_block,
            'id_modul'=>$request->modul,
            'tipe_jadwal'=>$request->tipe_jadwal,
            'id_mata_kuliah'=>$request->id_mata_kuliah,
            'id_ruangan'=>$request->id_ruangan]);

        //UNTUK MEMBUAT JADWAL DOSEN YANG BERKAIT SAMA PENJADWALAN
            foreach ($request->id_user as $user_dosen) { 
                $jadwal_dosen = Jadwal_dosen::create([ 
                    'id_jadwal' =>$penjadwalan->id,
                    'id_dosen'=>$user_dosen,
                    'id_block'=>$request->id_block,
                    'id_mata_kuliah'=>$request->id_mata_kuliah,
                    'id_ruangan'=>$request->id_ruangan,
                    'tanggal' =>$request->tanggal,
                    'waktu_mulai'=>$data_setting_waktu[0],
                    'waktu_selesai'=>$data_setting_waktu[1],
                    ]);                
            }

        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Dosen Berhasil Di Gantikan"
            ]);
        return redirect()->route('penjadwalans.index');
    }

    public function edit($id)
    {
        //MENYIAPKAN DATA UNTUNG DI TAMPILKAN DI EDIT PENJDWALAN
        $penjadwalans = Penjadwalan::find($id);
        //MENAMPILKAN USER YANG OTORITAS DOSEN
        $users = DB::table('users')
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id',2)
            ->pluck('name','id');

        //MENAMPILKAN BLOCK SESUAI DENGAN YANG LOGIN(APABILA PJ DOSEN YANG LOGIN MAKA BLOCK NYA YANG MUNCUL USER PJ DOSEN YANG ADA DI BLOK DAN APABILA ADMIN MAKA MUNCUL SEMUA BLOCK)
        $pj_dosen = Auth::user()->id;
        $data_block = DB::table('master_blocks')
            ->leftJoin('user_pj_dosens', 'master_blocks.id', '=', 'user_pj_dosens.id_master_block')
            ->where('user_pj_dosens.id_pj_dosen',$pj_dosen)
            ->pluck('master_blocks.nama_block','master_blocks.id'); 
        //MENAMPILKAN JADWAL DOSEN SESUAI YANG ADA DI PENJADWALAN
        $jadwal_dosen = DB::table('jadwal_dosens')
            ->leftJoin('users', 'users.id', '=', 'jadwal_dosens.id_dosen')
            ->where('jadwal_dosens.id_jadwal',$id)
            ->pluck('users.name','users.id'); 
            
            $dosen = Jadwal_dosen::with(['jadwal','dosen'])->where('id_jadwal',$id)->get(); 
            $data_dosen = '';
            foreach ($dosen as $dosens) { 
              $data_dosen .= ( "'".$dosens->dosen->id ."'," ); //untuk menampilkan data user yang sesuai ketika tambah
            }    
        //MENAMPILKAN WAKTU SUSAI YANG ADA DI PENJADWALAN
         $data_waktu = substr($penjadwalans->waktu_mulai, 0, -3) ." - ".substr($penjadwalans->waktu_selesai, 0, -3);

        //MENAMPILKAN MODUL BLOK YANG ADA DI PENJADWALAN
        $modul = ModulBlok::leftJoin('moduls','moduls.id','=','modul_bloks.id_modul')->where('id_blok',$penjadwalans->id_block)->pluck('moduls.nama_modul','modul_bloks.id_modul_blok');
      
        return view('penjadwalans.edit',['users' => $users,'data_waktu' => $data_waktu,'data_block'=>$data_block])->with(compact('penjadwalans','data_dosen','modul')); 
    }

    //PROSES UPDATE PENJADWALAN
    public function update(Request $request, $id)
    {
        //NGAMBIL DATA DARI FORM EDIT
         $this->validate($request, [
            'tanggal'   => 'required',
            'data_waktu'     => 'required',
            'id_block'    => 'required|exists:master_blocks,id',
            'id_ruangan'    => 'required|exists:master_ruangans,id',
            'id_user'    => 'required|exists:users,id',
            'modul'    => 'required',
            'tipe_jadwal'    => 'required',
            ]); 

          //jika tipe jadwalnya bukan kuliah atau praktikum maka mata kuliah nya -
        if ($request->id_mata_kuliah == NULL) {
            $request->id_mata_kuliah = "-";
        }

         //MEMISAHKAN WAKTU MULAI DAN SELESAI
        $data_setting_waktu = explode("-",$request->data_waktu);
            //MENGECEK DATA YANG SAMA APA TIDAK
            $penjadwalans = Penjadwalan::find($id); 
            $data_penjadwalan = Penjadwalan::statusRuanganEdit($request,$data_setting_waktu,$id); 
            //APABILA $data_penjadwalan == 0 maka ngecek dosen
            if ($data_penjadwalan->count() == 0) {
                $dosen_punya_jadwal = array();
                foreach ($request->id_user as $user_dosen) {
                 $jadwal_dosen = Jadwal_dosen::statusDosenEdit($request,$user_dosen,$data_setting_waktu,$id); 
                 $data_jadwal_dosen = $jadwal_dosen->first(); 

                    if ($jadwal_dosen->count() > 0) {
                        array_push($dosen_punya_jadwal, ['id_jadwal'=>$data_jadwal_dosen->id_jadwal,'id_dosen'=>$data_jadwal_dosen->id_dosen]);
                    }
                } 
            //APABILA JADWAL NYA SAMA MAKA MUNCUL PERINGATAN
                if (count($dosen_punya_jadwal) > 0 ) { 
                    $message = 'Tidak Bisa Menambahkan Dosen Berikut Karena Sudah Memiliki Jadwal :<ul>'; 
                        foreach ($dosen_punya_jadwal as $dosen_punya_jadwals) {  
                            $nama_dosen = User::find($dosen_punya_jadwals['id_dosen']);
                            $data_penjadwalans = Penjadwalan::find($dosen_punya_jadwals['id_jadwal']); 

                            if ($data_penjadwalans->id_mata_kuliah == NULL OR $data_penjadwalans->id_mata_kuliah == '-') {
                            $message .= "<li><b>$nama_dosen->name</b> Memilik Jadwal Di Ruangan <b>".$data_penjadwalans->ruangan->nama_ruangan." </b> Block <b>".$data_penjadwalans->block->nama_block."</b></li>";
                            }else{
                            $message .= "<li><b>$nama_dosen->name</b> Memilik Jadwal Di Ruangan <b>".$data_penjadwalans->ruangan->nama_ruangan."</b> Block <b>".$data_penjadwalans->block->nama_block."</b>  Mata Kuliah <b>". $data_penjadwalans->mata_kuliah->nama_mata_kuliah." </b> </li>";   
                            }
                        }
                    $message .= '</ul>';

                    Session::flash("flash_notification", [
                        "level"=>"danger",
                        "message"=>"$message"
                        ]); 
                    return redirect()->back()->withInput();
                }
            }
            else{ 
            //APABILA RUANGAN SUDAH DI PAKAI MAKA MUNCUL PERINGATAN 
                $data_ruangan =  Master_ruangan::find($request->id_ruangan);
                $data_block = Master_block::find($request->id_block);
                $data_mata_kuliah = Master_mata_kuliah::find($data_penjadwalan->first()->id_mata_kuliah);
                
                if ($data_penjadwalan->first()->id_mata_kuliah == NULL OR $data_penjadwalan->first()->id_mata_kuliah == '-') { 
                    Session::flash("flash_notification", [
                        "level"=>"danger",
                        "message"=>"Ruangan $data_ruangan->nama_ruangan Sudah Di Pakai Block $data_block->nama_block"
                        ]);
                }else{
                    Session::flash("flash_notification", [
                        "level"=>"danger",
                        "message"=>"Ruangan $data_ruangan->nama_ruangan Sudah Di Pakai Block $data_block->nama_block Mata Kuliah $data_mata_kuliah->nama_mata_kuliah"
                        ]);
                }
                return redirect()->back()->withInput();
            } 
       
        //JIKA PENJADWALAN TIDAK ADA YANG SAMA MAKA PROSES TAMBAH PENJADWALAN BERHASIL
        $penjadwalan = Penjadwalan::where('id', $id)->update([ 
            'tanggal' =>$request->tanggal,
            'waktu_mulai'=>$data_setting_waktu[0] ,
            'waktu_selesai'=>$data_setting_waktu[1] ,
            'id_block'=>$request->id_block,
            'id_mata_kuliah'=>$request->id_mata_kuliah,
            'id_modul'=>$request->modul,
            'tipe_jadwal'=>$request->tipe_jadwal,
            'id_ruangan'=>$request->id_ruangan]);
            //MENGHAPUS JADWAL DOSEN
            Jadwal_dosen::where('id_jadwal', $id)->delete(); 
            //MEMBUAT BARU JADWAL DOSEN
            foreach ($request->id_user as $user_dosen) {
                # code...
                $jadwal_dosen = Jadwal_dosen::create([ 
                    'id_jadwal' =>$id,
                    'id_dosen'=>$user_dosen,
                    'id_block'=>$request->id_block,
                    'id_mata_kuliah'=>$request->id_mata_kuliah,
                    'id_ruangan'=>$request->id_ruangan,
                      'tanggal' =>$request->tanggal,
            'waktu_mulai'=>$data_setting_waktu[0],
            'waktu_selesai'=>$data_setting_waktu[1],
                    ]);
                
            }

        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Penjadwalan Berhasil Di Ubah"
            ]);
        return redirect()->route('penjadwalans.index');
    }
 
    public function destroy($id)
    {
        //PROSES HAPUS PENJADWALAN
         if(!Penjadwalan::destroy($id)) 
        {
        Session:: flash("flash_notification", [
            "level"=>"danger",
            "message"=>"Penjadwalan Tidak Berhasil Di Hapus"
            ]);
            return redirect()->back();
        }
        else{
        Jadwal_dosen::where('id_jadwal', $id)->delete();
        Session:: flash("flash_notification", [
            "level"=>"danger",
            "message"=>"Penjadwalan berhasil dihapus"
            ]);
        return redirect()->route('penjadwalans.index');
            }
    }


    
}
