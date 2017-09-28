<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Penjadwalan;
use App\ModulBlok;
use App\Master_block;
use Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Html\Builder;
use App\Jadwal_dosen;
use Yajra\Datatables\Datatables;
use Jenssegers\Agent\Agent;
use App\UserPjDosen;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,Builder $htmlBuilder)
    {
        $agent = new Agent();
        $bulan_sekarang = date('m');// bulan sekarang
                // query untuk menghitung penjadwalan perperiode
        $penjadwalans   = Penjadwalan::select(DB::raw('count(*) as jumlah_data, status_jadwal'))
                        ->whereMonth('tanggal',$bulan_sekarang)
                        ->groupBy('status_jadwal')
                        ->get();

        $jadwal_terlaksana = 0;
        $jadwal_belum_terlaksana = 0;
        $jadwal_batal = 0;
        $jadwal_ubah_dosen = 0; 

        foreach ($penjadwalans as $penjadwalan) {
           
                if ($penjadwalan->status_jadwal == 0 ) {
                    
                    $jadwal_belum_terlaksana = $jadwal_belum_terlaksana + $penjadwalan->jumlah_data;
                }
                if ($penjadwalan->status_jadwal == 1) {
                    
                    $jadwal_terlaksana = $jadwal_terlaksana + $penjadwalan->jumlah_data;
                }
                if ($penjadwalan->status_jadwal == 2) {
   
                    $jadwal_batal = $jadwal_batal + $penjadwalan->jumlah_data;
                } 
                if ($penjadwalan->status_jadwal == 3) {
   
                    $jadwal_ubah_dosen = $jadwal_ubah_dosen + $penjadwalan->jumlah_data;
                } 

        }
     
       if (!Auth::check()) {
        return redirect()->route('login');
        }

        $user_otoritas = Auth::user()->roles->first()->name;
        switch ($user_otoritas) {
            case 'admin':
                
              
                return view('home',['jadwal_terlaksana'=>$jadwal_terlaksana,'jadwal_belum_terlaksana'=>$jadwal_belum_terlaksana,'jadwal_batal'=>$jadwal_batal,'jadwal_ubah_dosen'=>$jadwal_ubah_dosen,'agent' => $agent]);

                break;  
            case 'dosen':
                return HomeController::jadwal_kuliah_dosen($request,$htmlBuilder);

                break; 
             case 'mahasiswa':
                # code...

              return HomeController::jadwal_kuliah_mahasiswa();
                


                break; 
             case 'pimpinan':
                # code...
                return view('home',['jadwal_terlaksana'=>$jadwal_terlaksana,'jadwal_belum_terlaksana'=>$jadwal_belum_terlaksana,'jadwal_batal'=>$jadwal_batal,'agent' => $agent]);
                break;
             case 'pj_dosen':
                # code...
                return HomeController::halaman_pj_dosen($request,$htmlBuilder);
                break;
            
            default:
                # code...
                break;
        }

    }  

    public function halaman_pj_dosen(Request $request, Builder $htmlBuilder){
        if ($request->ajax()) {
            # code...
            $penjadwalans = Penjadwalan::with(['block','mata_kuliah','ruangan','modul']);
            return Datatables::of($penjadwalans)->addColumn('action', function($penjadwalan){

                $pj_dosen = UserPjDosen::where('id_master_block',$penjadwalan->id_block)->where('id_pj_dosen',Auth::user()->id)->count();

                if ($pj_dosen > 0) {
                       return view('datatable._action', [
                        'model'     => $penjadwalan,
                        'form_url'  => route('penjadwalans.destroy', $penjadwalan->id),
                        'edit_url'  => route('penjadwalans.edit', $penjadwalan->id),
                        'confirm_message'   => 'Apakah Anda Yakin Mau Menghapus Penjadwalan ?'
                        ]);
                }
                else {
                    return "-";
                }

                 
                })
            ->addColumn('jadwal_dosen', function($jadwal){
                $jadwal_dosens = Jadwal_dosen::with(['jadwal','dosen'])->where('id_jadwal',$jadwal->id)->get(); 
                    return view('penjadwalans._action', [ 
                        'model_user'     => $jadwal_dosens,
                        'id_jadwal' => $jadwal->id
                        ]);
                })
            ->addColumn('tombol_status', function($data_status){  
                    return view('penjadwalans._action_status', [ 
                        'model'     => $data_status,
                        'terlaksana_url' => route('penjadwalans.terlaksana', $data_status->id),
                        'belum_terlaksana_url' => route('penjadwalans.belumterlaksana', $data_status->id),
                        'batal_url' => route('penjadwalans.batal', $data_status->id),
                        'terlaksana_message'   => 'Apakah Anda Yakin Penjadwalan Terlaksana ?',
                        'belum_terlaksana_message'   => 'Apakah Anda Yakin Penjadwalan Belum Terlaksana?',
                        'batal_message'   => 'Apakah Anda Yakin Mau Membatalakan Penjadwalan ?',
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
        $html = $htmlBuilder
        ->addColumn(['data' => 'tanggal', 'name' => 'tanggal', 'title' => 'Tanggal'])         
        ->addColumn(['data' => 'waktu_mulai', 'name' => 'waktu_mulai', 'title' => 'Mulai'])  
        ->addColumn(['data' => 'waktu_selesai', 'name' => 'waktu_selesai', 'title' => 'Selesai'])         
        ->addColumn(['data' => 'block.nama_block', 'name' => 'block.nama_block', 'title' => 'Block', 'orderable' => false, ])
        ->addColumn(['data' => 'mata_kuliah', 'name' => 'mata_kuliah', 'title' => 'Mata Kuliah', 'orderable' => false, ])  
        ->addColumn(['data' => 'ruangan.nama_ruangan', 'name' => 'ruangan.nama_ruangan', 'title' => 'Ruangan', 'orderable' => false, ])    
        ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status Penjadwalan', 'orderable' => false, 'searchable'=>false])    
        ->addColumn(['data' => 'tombol_status', 'name' => 'tombol_status', 'title' => '', 'orderable' => false, 'searchable'=>false])   
        ->addColumn(['data' => 'jadwal_dosen', 'name' => 'jadwal_dosen', 'title' => 'Dosen', 'orderable' => false, 'searchable'=>false])     
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable'=>false]);

        $users = DB::table('users')
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id',2)
            ->pluck('name','id');

           $users->prepend('Semua Dosen', 'semua');

        return view('penjadwalans.index',['users'=> $users])->with(compact('html'));
    }



    public function table_terlaksana(Request $request){

       
       if ($request->id_block == 'Semua' AND $request->tipe_jadwal != 'Semua') {
            # code...

        $penjadwalans   = Penjadwalan::with(['block','mata_kuliah','ruangan'])
                        ->where('tanggal','>=',$request->dari_tanggal)
                        ->where('tanggal','<=',$request->sampai_tanggal) 
                        ->where('tipe_jadwal',$request->tipe_jadwal)
                        ->where('status_jadwal',1)
                        ->get();

        }elseif ($request->tipe_jadwal == 'Semua' AND $request->id_block != 'Semua') {
            # code...

        $penjadwalans   = Penjadwalan::with(['block','mata_kuliah','ruangan'])
                        ->where('tanggal','>=',$request->dari_tanggal)
                        ->where('tanggal','<=',$request->sampai_tanggal) 
                        ->where('id_block',$request->id_block)
                        ->where('status_jadwal',1)
                        ->get();
        }elseif ($request->id_block == 'Semua' AND $request->tipe_jadwal == 'Semua') {
            # code...
        $penjadwalans   = Penjadwalan::with(['block','mata_kuliah','ruangan'])
                        ->where('tanggal','>=',$request->dari_tanggal)
                        ->where('tanggal','<=',$request->sampai_tanggal)  
                        ->where('status_jadwal',1)
                        ->get();
        }
        else{
        
        $bulan_sekarang = date('m');// bulan sekarang
                    // query untuk menghitung penjadwalan bulan ini
        $penjadwalans = Penjadwalan::with(['block','mata_kuliah','ruangan'])
                        ->whereMonth('tanggal',$bulan_sekarang)
                        ->where('status_jadwal',1);
        } 

        return Datatables::of($penjadwalans)->addColumn('jadwal_dosen', function($jadwal){
                $jadwal_dosens = Jadwal_dosen::with(['jadwal','dosen'])->where('id_jadwal',$jadwal->id)->get(); 
                    return view('penjadwalans._action', [ 
                        'model_user'     => $jadwal_dosens,
                        'id_jadwal' => $jadwal->id
                        ]);
                })->make(true);

    }  


      public function table_belum_terlaksana(Request $request){

        
       if ($request->id_block == 'Semua' AND $request->tipe_jadwal != 'Semua') {
            # code...

        $penjadwalans   = Penjadwalan::with(['block','mata_kuliah','ruangan'])
                        ->where('tanggal','>=',$request->dari_tanggal)
                        ->where('tanggal','<=',$request->sampai_tanggal) 
                        ->where('tipe_jadwal',$request->tipe_jadwal)
                        ->where('status_jadwal',0)
                        ->get();

        }elseif ($request->tipe_jadwal == 'Semua' AND $request->id_block != 'Semua') {
            # code...

        $penjadwalans   = Penjadwalan::with(['block','mata_kuliah','ruangan'])
                        ->where('tanggal','>=',$request->dari_tanggal)
                        ->where('tanggal','<=',$request->sampai_tanggal) 
                        ->where('id_block',$request->id_block)
                        ->where('status_jadwal',0)
                        ->get();
        }elseif ($request->id_block == 'Semua' AND $request->tipe_jadwal == 'Semua') {
            # code...
        $penjadwalans   = Penjadwalan::with(['block','mata_kuliah','ruangan'])
                        ->where('tanggal','>=',$request->dari_tanggal)
                        ->where('tanggal','<=',$request->sampai_tanggal)  
                        ->where('status_jadwal',0)
                        ->get();
        }
        else{
        
        $bulan_sekarang = date('m');// bulan sekarang
                    // query untuk menghitung penjadwalan bulan ini
        $penjadwalans = Penjadwalan::with(['block','mata_kuliah','ruangan'])
                        ->whereMonth('tanggal',$bulan_sekarang)
                        ->where('status_jadwal',0);
        }

        return Datatables::of($penjadwalans)->addColumn('jadwal_dosen', function($jadwal){
                $jadwal_dosens = Jadwal_dosen::with(['jadwal','dosen'])->where('id_jadwal',$jadwal->id)->get(); 
                    return view('penjadwalans._action', [ 
                        'model_user'     => $jadwal_dosens,
                        'id_jadwal' => $jadwal->id
                        ]);
                })->make(true);

    }

    public function table_batal(Request $request){

        
       if ($request->id_block == 'Semua' AND $request->tipe_jadwal != 'Semua') {
            # code...

        $penjadwalans   = Penjadwalan::with(['block','mata_kuliah','ruangan'])
                        ->where('tanggal','>=',$request->dari_tanggal)
                        ->where('tanggal','<=',$request->sampai_tanggal) 
                        ->where('tipe_jadwal',$request->tipe_jadwal)
                        ->where('status_jadwal',2)
                        ->get();

        }elseif ($request->tipe_jadwal == 'Semua' AND $request->id_block != 'Semua') {
            # code...

        $penjadwalans   = Penjadwalan::with(['block','mata_kuliah','ruangan'])
                        ->where('tanggal','>=',$request->dari_tanggal)
                        ->where('tanggal','<=',$request->sampai_tanggal) 
                        ->where('id_block',$request->id_block)
                        ->where('status_jadwal',2)
                        ->get();
        }elseif ($request->id_block == 'Semua' AND $request->tipe_jadwal == 'Semua') {
            # code...
        $penjadwalans   = Penjadwalan::with(['block','mata_kuliah','ruangan'])
                        ->where('tanggal','>=',$request->dari_tanggal)
                        ->where('tanggal','<=',$request->sampai_tanggal)  
                        ->where('status_jadwal',2)
                        ->get();
        }
        else{
        
        $bulan_sekarang = date('m');// bulan sekarang
                    // query untuk menghitung penjadwalan bulan ini
        $penjadwalans = Penjadwalan::with(['block','mata_kuliah','ruangan'])
                        ->whereMonth('tanggal',$bulan_sekarang)
                        ->where('status_jadwal',2);
        }
        return Datatables::of($penjadwalans)->addColumn('jadwal_dosen', function($jadwal){
                $jadwal_dosens = Jadwal_dosen::with(['jadwal','dosen'])->where('id_jadwal',$jadwal->id)->get(); 
                    return view('penjadwalans._action', [ 
                        'model_user'     => $jadwal_dosens,
                        'id_jadwal' => $jadwal->id
                        ]);
                })->make(true);

    }

    public function table_ubah_dosen(Request $request){

        
       if ($request->id_block == 'Semua' AND $request->tipe_jadwal != 'Semua') {
            # code...

        $penjadwalans   = Penjadwalan::with(['block','mata_kuliah','ruangan'])
                        ->where('tanggal','>=',$request->dari_tanggal)
                        ->where('tanggal','<=',$request->sampai_tanggal) 
                        ->where('tipe_jadwal',$request->tipe_jadwal)
                        ->where('status_jadwal',3)
                        ->get();

        }elseif ($request->tipe_jadwal == 'Semua' AND $request->id_block != 'Semua') {
            # code...

        $penjadwalans   = Penjadwalan::with(['block','mata_kuliah','ruangan'])
                        ->where('tanggal','>=',$request->dari_tanggal)
                        ->where('tanggal','<=',$request->sampai_tanggal) 
                        ->where('id_block',$request->id_block)
                        ->where('status_jadwal',3)
                        ->get();
        }elseif ($request->id_block == 'Semua' AND $request->tipe_jadwal == 'Semua') {
            # code...
        $penjadwalans   = Penjadwalan::with(['block','mata_kuliah','ruangan'])
                        ->where('tanggal','>=',$request->dari_tanggal)
                        ->where('tanggal','<=',$request->sampai_tanggal)  
                        ->where('status_jadwal',3)
                        ->get();
        }
        else{
        
        $bulan_sekarang = date('m');// bulan sekarang
                    // query untuk menghitung penjadwalan bulan ini
        $penjadwalans = Penjadwalan::with(['block','mata_kuliah','ruangan'])
                        ->whereMonth('tanggal',$bulan_sekarang)
                        ->where('status_jadwal',3);
        }
        return Datatables::of($penjadwalans)->addColumn('jadwal_dosen', function($jadwal){
                $jadwal_dosens = Jadwal_dosen::with(['jadwal','dosen'])->where('id_jadwal',$jadwal->id)->get(); 
                    return view('penjadwalans._action', [ 
                        'model_user'     => $jadwal_dosens,
                        'id_jadwal' => $jadwal->id
                        ]);
                })->make(true);

    }

        public function analisa_jadwal(Request $request){
        
        $this->validate($request, [
            'dari_tanggal'     => 'required',
            'sampai_tanggal'     => 'required',
            'id_block'     => 'required' ,
            'tipe_jadwal'     => 'required' 

        ]);  

        $agent = new Agent();
        // query untuk menghitung penjadwalan perperiode
        if ($request->id_block == 'Semua' AND $request->tipe_jadwal != 'Semua') {
            # code...

        $penjadwalans   = Penjadwalan::select(DB::raw('count(*) as jumlah_data, status_jadwal'))
                        ->where('tanggal','>=',$request->dari_tanggal)
                        ->where('tanggal','<=',$request->sampai_tanggal) 
                        ->where('tipe_jadwal',$request->tipe_jadwal)
                        ->groupBy('status_jadwal')
                        ->get(); 
 
        }
        elseif ($request->tipe_jadwal == 'Semua' AND $request->id_block != 'Semua') {
            # code...

        $penjadwalans   = Penjadwalan::select(DB::raw('count(*) as jumlah_data, status_jadwal'))
                        ->where('tanggal','>=',$request->dari_tanggal)
                        ->where('tanggal','<=',$request->sampai_tanggal) 
                        ->where('id_block',$request->id_block)
                        ->groupBy('status_jadwal')
                        ->get();
        }elseif ($request->id_block == 'Semua' AND $request->tipe_jadwal == 'Semua') {
            # code...
        $penjadwalans   = Penjadwalan::select(DB::raw('count(*) as jumlah_data, status_jadwal'))
                        ->where('tanggal','>=',$request->dari_tanggal)
                        ->where('tanggal','<=',$request->sampai_tanggal)  
                        ->groupBy('status_jadwal')
                        ->get();
        }
        else{

        $penjadwalans   = Penjadwalan::select(DB::raw('count(*) as jumlah_data, status_jadwal'))
                        ->where('tanggal','>=',$request->dari_tanggal)
                        ->where('tanggal','<=',$request->sampai_tanggal)
                        ->where('id_block',$request->id_block)
                        ->where('tipe_jadwal',$request->tipe_jadwal)
                        ->groupBy('status_jadwal')
                        ->get();

        }

        $jadwal_terlaksana = 0;
        $jadwal_belum_terlaksana = 0;
        $jadwal_batal = 0; 
        $jadwal_ubah_dosen = 0; 
        $bulan_sekarang = date('m');// bulan sekarang

        foreach ($penjadwalans as $penjadwalan) {
           
                if ($penjadwalan->status_jadwal == 0 ) {
                    
                    $jadwal_belum_terlaksana = $jadwal_belum_terlaksana + $penjadwalan->jumlah_data;
                }
                if ($penjadwalan->status_jadwal == 1) {
                    
                    $jadwal_terlaksana = $jadwal_terlaksana + $penjadwalan->jumlah_data;
                }
                if ($penjadwalan->status_jadwal == 2) {
   
                    $jadwal_batal = $jadwal_batal + $penjadwalan->jumlah_data;
                } 
                if ($penjadwalan->status_jadwal == 3) {
   
                    $jadwal_ubah_dosen = $jadwal_ubah_dosen + $penjadwalan->jumlah_data;
                } 

        }
                        # code...
                return view('home',['jadwal_terlaksana'=>$jadwal_terlaksana,'jadwal_belum_terlaksana'=>$jadwal_belum_terlaksana,'jadwal_batal'=>$jadwal_batal,'jadwal_ubah_dosen'=>$jadwal_ubah_dosen,'dari_tanggal'=>$request->dari_tanggal,'sampai_tanggal'=>$request->sampai_tanggal,'agent' => $agent,'bulan_sekarang'=>$bulan_sekarang]);


    }

    public function jadwal_kuliah_dosen(Request $request, Builder $htmlBuilder){

           if ($request->ajax()) {
            # code...


        $dosen = Auth::user()->id;


            if (isset($request->dari_tanggal)) {
                       $penjadwalans = Jadwal_dosen::with(['block','mata_kuliah','ruangan','jadwal'])->where('id_dosen',$dosen)->where('tanggal' ,'>=',$request->dari_tanggal)->where('tanggal','<=',$request->sampai_tanggal)->groupBy('id_jadwal');
            }
            else {
                 $penjadwalans = Jadwal_dosen::with(['block','mata_kuliah','ruangan'])->where('id_dosen',$dosen)->groupBy('id_jadwal');
            }
     

            return Datatables::of($penjadwalans)
            ->addColumn('jadwal_dosen', function($jadwal){
                $jadwal_dosens = Jadwal_dosen::with(['jadwal','dosen'])->where('id_jadwal',$jadwal->id_jadwal)->get(); 
                    return view('penjadwalans._action', [ 
                        'model_user'     => $jadwal_dosens,
                        'id_jadwal' => $jadwal->id
                        ]);
                })
            ->addColumn('tombol_status', function($data_status){  
                    return view('penjadwalans._action_status', [ 
                        'model'     => $data_status,
                        'asal_input'     => 1,
                        'terlaksana_url' => route('penjadwalans.terlaksana', $data_status->id),
                        'belum_terlaksana_url' => route('penjadwalans.belumterlaksana', $data_status->id),
                        'batal_url' => route('penjadwalans.batal_dosen'),
                        'terlaksana_message'   => 'Apakah Anda Yakin Penjadwalan Terlaksana ?',
                        'belum_terlaksana_message'   => 'Apakah Anda Yakin Penjadwalan Belum Terlaksana?',
                        'batal_message'   => 'Apakah Anda Yakin Mau Membatalakan Penjadwalan ?',
                        ]);
                })
            ->addColumn('status_jadwal',function($status_penjadwalan){
               
                if ($status_penjadwalan->jadwal->status_jadwal == 0 ) {
                    # code...
                    $status = "Belum Terlaksana";
                }
                elseif ($status_penjadwalan->jadwal->status_jadwal == 1) {
                    # code...
                     $status = "Sudah Terlaksana";
                }
                elseif ($status_penjadwalan->jadwal->status_jadwal == 2) {
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
        $html = $htmlBuilder
        ->addColumn(['data' => 'tanggal', 'name' => 'tanggal', 'title' => 'Tanggal'])         
        ->addColumn(['data' => 'waktu_mulai', 'name' => 'waktu_mulai', 'title' => 'Mulai'])  
        ->addColumn(['data' => 'waktu_selesai', 'name' => 'waktu_selesai', 'title' => 'Selesai'])         
        ->addColumn(['data' => 'block.nama_block', 'name' => 'block.nama_block', 'title' => 'Block', 'orderable' => false, ])
        ->addColumn(['data' => 'mata_kuliah', 'name' => 'mata_kuliah', 'title' => 'Mata Kuliah', 'orderable' => false, ])  
        ->addColumn(['data' => 'ruangan.nama_ruangan', 'name' => 'ruangan.nama_ruangan', 'title' => 'Ruangan', 'orderable' => false, ])    
        ->addColumn(['data' => 'status_jadwal', 'name' => 'status_jadwal', 'title' => 'Status Penjadwalan', 'orderable' => false, 'searchable'=>false])
        ->addColumn(['data' => 'tombol_status', 'name' => 'tombol_status', 'title' => '', 'orderable' => false, 'searchable'=>false])
        ->addColumn(['data' => 'jadwal_dosen', 'name' => 'jadwal_dosen', 'title' => 'Dosen', 'orderable' => false, 'searchable'=>false]);


        return view('dosen.index')->with(compact('html'));

    }

    public function jadwal_kuliah_mahasiswa(){

        $mahasiswa = Auth::user()->id_angkatan;
        $id_mahasiswa = Auth::user()->id;
        
        $block = DB::table('master_blocks') 
                    ->leftJoin('mahasiswa_block', 'mahasiswa_block.id_block', '=', 'master_blocks.id') 
                    ->where('mahasiswa_block.id_mahasiswa',$id_mahasiswa) 
                    ->orWhere('master_blocks.id_angkatan',$mahasiswa)
                    ->pluck('master_blocks.nama_block','master_blocks.id'); 

        return view('mahasiswa.index',['block' => $block]);
    }

    public function data_modul_perblock (Request $request){
        if ($request->ajax()) {
            
            $modul =  ModulBlok::with('modul')->where('id_blok',$request->block)->get();

            foreach ($modul as $data) {
                echo "<option value='".$data->id_modul."'>".$data->modul->nama_modul."</option>";
            }

        }
    }

    public function info_jadwal(Request $request){
        if ($request->ajax()) {
            $penjadwalans = Penjadwalan::with('mata_kuliah','ruangan','block')->where('id',$request->id_jadwal)->first();
            $user = Auth::user();
            $link =  url('admin/penjadwalans/'.$penjadwalans->id.'/edit');
                  $isi_event = "
                <p>";
                if($user->hasRole(['admin', 'pj_dosen']))
                {
                    $isi_event .= "Apabila Ingin Mengubah Penjadwalan Klik <a class='btn btn-sm btn-success' href=".$link." target='_blank'>Ubah</a> <br>";
                }else{
                   $isi_event .=  "";
                }
                    

               $isi_event .=  "Waktu : ".$penjadwalans->tanggal." ". $penjadwalans->waktu_mulai ."-". $penjadwalans->waktu_selesai ." <br>
                Block : ".$penjadwalans->block->nama_block ."<br>
                Ruangan :". $penjadwalans->ruangan->nama_ruangan ."<br>
                Mata Kuliah : ".$penjadwalans->mata_kuliah->nama_mata_kuliah . "<br>
                Dosen: <br>

                <ulstyle=list-style-type:circle'>";
                $jadwal_dosens = Jadwal_dosen::with(['dosen'])->where('id_jadwal',$request->id_jadwal)->get(); 
                foreach ($jadwal_dosens as $data) {
                    $isi_event .= "<li>".$data->dosen->name."</li>";
                }    
                $isi_event .="</ul> 
                </p>";
                return $isi_event;
        }
   
    }
    public function proses_jadwal_mahasiswa(Request $request){
        

        $this->validate($request, [
            'block'     => 'required',
            'modul'     => 'required' ]);  

        $modul = ModulBlok::with('modul')->where('id_blok',$request->block)->where('id_modul',$request->modul)->first();

        $penjadwalan = Penjadwalan::with('mata_kuliah','block','ruangan')->where('id_block',$request->block)->where('tanggal','>=',$modul->dari_tanggal)->where('tanggal','<=',$modul->sampai_tanggal)->get();

        $jadwal_senin = array();
        $jadwal_selasa = array();
        $jadwal_rabu = array();
        $jadwal_kamis = array();
        $jadwal_jumat = array();


        foreach ($penjadwalan as $penjadwalans) {
            

                if ($penjadwalans->id_mata_kuliah == "-" OR $penjadwalans->id_mata_kuliah == ""  OR $penjadwalans->id_mata_kuliah == "0") {
                    $mata_kuliah = "-";
                }
                else {
                    $mata_kuliah = $penjadwalans->mata_kuliah->nama_mata_kuliah;
                }

            $timestamp = strtotime($penjadwalans->tanggal);
            $day = date('w', $timestamp);


            switch ($day) {
                case '1':
                  

                array_push($jadwal_senin, ['id_jadwal'=> $penjadwalans->id ,'waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $mata_kuliah,'tipe_jadwal'=>$penjadwalans->tipe_jadwal]);
                
                    break; 
                 case '2':
                 

                array_push($jadwal_selasa, ['id_jadwal'=> $penjadwalans->id ,'waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $mata_kuliah,'tipe_jadwal'=>$penjadwalans->tipe_jadwal]);

                    break;    
                 case '3':
                  
                array_push($jadwal_rabu, ['id_jadwal'=> $penjadwalans->id ,'waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $mata_kuliah,'tipe_jadwal'=>$penjadwalans->tipe_jadwal]);

                    break;    
                 case '4':
                   
                array_push($jadwal_kamis, ['id_jadwal'=> $penjadwalans->id ,'waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $mata_kuliah,'tipe_jadwal'=>$penjadwalans->tipe_jadwal]);

                    break;    
                 case '5':
                  
                array_push($jadwal_jumat, ['id_jadwal'=> $penjadwalans->id ,'waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $mata_kuliah,'tipe_jadwal'=>$penjadwalans->tipe_jadwal]);

                    break;
                
                default:
                    
                    break;
            }

        }

       $mahasiswa = Auth::user()->id;
        $id_mahasiswa = Auth::user()->id;
        
        $block = DB::table('master_blocks') 
                    ->leftJoin('mahasiswa_block', 'mahasiswa_block.id_block', '=', 'master_blocks.id') 
                    ->where('mahasiswa_block.id_mahasiswa',$id_mahasiswa) 
                    ->orWhere('master_blocks.id_angkatan',Auth::user()->id_angkatan)
                    ->pluck('master_blocks.nama_block','master_blocks.id'); 

        return view('mahasiswa.index_schedule',['jadwal_senin'=> $jadwal_senin,'jadwal_selasa' => $jadwal_selasa,'jadwal_rabu' => $jadwal_rabu,'jadwal_kamis' => $jadwal_kamis,'jadwal_jumat' => $jadwal_jumat,'modul' => $modul,'mahasiswa' => $mahasiswa,'block' =>$block]);

    }

    public function jadwal_kuliah()
    {

        $block = Master_block::find(1);
        $modul = ModulBlok::with('modul')->where('id_blok',1)->first();
        $penjadwalan = Penjadwalan::with('mata_kuliah')->where('id_block',1)->where('tanggal','>=',$modul->dari_tanggal)->where('tanggal','<=',$modul->sampai_tanggal)->get();

        $jadwal_senin = array();
        $jadwal_selasa = array();
        $jadwal_rabu = array();
        $jadwal_kamis = array();
        $jadwal_jumat = array();

        foreach ($penjadwalan as $penjadwalans) {


                if ($penjadwalans->id_mata_kuliah == "-" OR $penjadwalans->id_mata_kuliah == ""  OR $penjadwalans->id_mata_kuliah == "0") {
                    $mata_kuliah = "-";
                }
                else {
                    $mata_kuliah = $penjadwalans->mata_kuliah->nama_mata_kuliah;
                }

            $timestamp = strtotime($penjadwalans->tanggal);
            $day = date('w', $timestamp);


            switch ($day) {
                case '1':
                  

                array_push($jadwal_senin, ['id_jadwal'=> $penjadwalans->id ,'waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $mata_kuliah,'tipe_jadwal'=>$penjadwalans->tipe_jadwal]);
                
                    break; 
                 case '2':
                 

                array_push($jadwal_selasa, ['id_jadwal'=> $penjadwalans->id ,'waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $mata_kuliah,'tipe_jadwal'=>$penjadwalans->tipe_jadwal]);

                    break;    
                 case '3':
                  
                array_push($jadwal_rabu, ['id_jadwal'=> $penjadwalans->id ,'waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $mata_kuliah,'tipe_jadwal'=>$penjadwalans->tipe_jadwal]);

                    break;    
                 case '4':
                   
                array_push($jadwal_kamis, ['id_jadwal'=> $penjadwalans->id ,'waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $mata_kuliah,'tipe_jadwal'=>$penjadwalans->tipe_jadwal]);

                    break;    
                 case '5':
                  
                array_push($jadwal_jumat, ['id_jadwal'=> $penjadwalans->id ,'waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $mata_kuliah,'tipe_jadwal'=>$penjadwalans->tipe_jadwal]);

                    break;
                
                default:
                    
                    break;
            }

        }
   

        return view('contoh_jadwal_kuliah',['jadwal_senin'=> $jadwal_senin,'jadwal_selasa' => $jadwal_selasa,'jadwal_rabu' => $jadwal_rabu,'jadwal_kamis' => $jadwal_kamis,'jadwal_jumat' => $jadwal_jumat,'modul' => $modul]);
    }
}
