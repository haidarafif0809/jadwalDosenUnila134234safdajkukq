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

class PenjadwalanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        //
        if ($request->ajax()) {
            # code...
            $penjadwalans = Penjadwalan::with(['block','mata_kuliah','ruangan','modul']);
            return Datatables::of($penjadwalans)->addColumn('action', function($penjadwalan){
                    return view('datatable._action', [
                        'model'     => $penjadwalan,
                        'form_url'  => route('penjadwalans.destroy', $penjadwalan->id),
                        'edit_url'  => route('penjadwalans.edit', $penjadwalan->id),
                        'confirm_message'   => 'Apakah Anda Yakin Mau Menghapus Penjadwalan ?'
                        ]);
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
                return $status;
                })->make(true);
        }
        $html = $htmlBuilder
        ->addColumn(['data' => 'tanggal', 'name' => 'tanggal', 'title' => 'Tanggal'])         
        ->addColumn(['data' => 'waktu_mulai', 'name' => 'waktu_mulai', 'title' => 'Mulai'])  
        ->addColumn(['data' => 'waktu_selesai', 'name' => 'waktu_selesai', 'title' => 'Selesai'])         
        ->addColumn(['data' => 'block.nama_block', 'name' => 'block.nama_block', 'title' => 'Block', 'orderable' => false, ])
        ->addColumn(['data' => 'mata_kuliah.nama_mata_kuliah', 'name' => 'mata_kuliah.nama_mata_kuliah', 'title' => 'Mata Kuliah', 'orderable' => false, ])  
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
                })->make(true);
        }
        $html = $htmlBuilder
        ->addColumn(['data' => 'tanggal', 'name' => 'tanggal', 'title' => 'Tanggal'])         
        ->addColumn(['data' => 'waktu_mulai', 'name' => 'waktu_mulai', 'title' => 'Mulai'])  
        ->addColumn(['data' => 'waktu_selesai', 'name' => 'waktu_selesai', 'title' => 'Selesai'])         
        ->addColumn(['data' => 'block.nama_block', 'name' => 'block.nama_block', 'title' => 'Block'])
        ->addColumn(['data' => 'mata_kuliah.nama_mata_kuliah', 'name' => 'mata_kuliah.nama_mata_kuliah', 'title' => 'Mata Kuliah'])  
        ->addColumn(['data' => 'ruangan.nama_ruangan', 'name' => 'ruangan.nama_ruangan', 'title' => 'Ruangan'])     
        ->addColumn(['data' => 'jadwal_dosen', 'name' => 'jadwal_dosen', 'title' => 'Dosen'])     
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable'=>false]);

   $users = DB::table('users')
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id',2)
            ->pluck('name','id');

           $users->prepend('Semua Dosen', 'semua');

        return view('penjadwalans.index',['users'=> $users])->with(compact('html'));
  
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // 
        $users = DB::table('users')
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id',2)
            ->pluck('name','id'); 
 
        return view('penjadwalans.create',['users' => $users]);
    }

    public function data_modul_perblock_penjadwalan (Request $request){
        if ($request->ajax()) {
            
            $modul =  ModulBlok::with('modul')->where('id_blok',$request->id_block)->get();

            foreach ($modul as $data) {
                echo "<option value='".$data->id_modul."'>".$data->modul->nama_modul."</option>";
            }

        }
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

        $this->validate($request, [
            'tanggal'   => 'required',
            'data_waktu'     => 'required', 
            'id_block'    => 'required|exists:master_blocks,id',
            'id_mata_kuliah'    => 'required|exists:master_mata_kuliahs,id',
            'id_ruangan'    => 'required|exists:master_ruangans,id',
            'id_user'    => 'required|exists:users,id',
            'modul'    => 'required|exists:users,id',
        ]);   

        $data_setting_waktu = explode("-",$request->data_waktu);

         

        $data_penjadwalan = Penjadwalan::statusRuangan($request,$data_setting_waktu)->count(); 
        if ($data_penjadwalan == 0) { 
            $dosen_punya_jadwal = array();
                foreach ($request->id_user as $user_dosen) {
                 $jadwal_dosen = Jadwal_dosen::statusDosen($request,$user_dosen,$data_setting_waktu); 
                 $data_jadwal_dosen = $jadwal_dosen->first(); 

                if ($jadwal_dosen->count() > 0) {
                    array_push($dosen_punya_jadwal, ['id_jadwal'=>$data_jadwal_dosen->id_jadwal,'id_dosen'=>$data_jadwal_dosen->id_dosen]);
                }
            } 
                if (count($dosen_punya_jadwal) > 0 ) { 
                    $message = 'Tidak Bisa Menambahkan Dosen Berikut Karena Sudah Memiliki Jadwal :<ul>'; 
                        foreach ($dosen_punya_jadwal as $dosen_punya_jadwals) {  
                            $nama_dosen = User::find($dosen_punya_jadwals['id_dosen']);
                            $data_penjadwalans = Penjadwalan::find($dosen_punya_jadwals['id_jadwal']);
                            $data_ruangan =  Master_ruangan::find($data_penjadwalans->id_ruangan);
                            $data_block = Master_block::find($data_penjadwalans->id_block);
                            $data_mata_kuliah = Master_mata_kuliah::find($data_penjadwalans->id_mata_kuliah);

                            $message .= "<li><b>$nama_dosen->name</b> Memilik Jadwal Di Ruangan <b>$data_ruangan->nama_ruangan</b> Block <b>$data_block->nama_block</b>  Mata Kuliah <b>$data_mata_kuliah->nama_mata_kuliah</b> </li>";
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

            $data_ruangan =  Master_ruangan::find($request->id_ruangan);
            $data_block = Master_block::find($request->id_block);
            $data_mata_kuliah = Master_mata_kuliah::find($request->id_mata_kuliah);

            Session::flash("flash_notification", [
                "level"=>"danger",
                "message"=>"Ruangan $data_ruangan->nama_ruangan Sudah Di Pakai Block $data_block->nama_block Mata Kuliah $data_mata_kuliah->nama_mata_kuliah"
                ]);
            return redirect()->back()->withInput();
        } 
  
        $penjadwalan = Penjadwalan::create([ 
            'tanggal' =>$request->tanggal,
            'waktu_mulai'=>$data_setting_waktu[0],
            'waktu_selesai'=>$data_setting_waktu[1],
            'id_block'=>$request->id_block,
            'id_modul'=>$request->modul,
            'id_mata_kuliah'=>$request->id_mata_kuliah,
            'id_ruangan'=>$request->id_ruangan]);

            foreach ($request->id_user as $user_dosen) {
                # code...
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
            "message"=>"Berhasil Menambah Penjadwalan"
            ]);
        if (isset($request->asal_input)) {
            # code...
            return redirect()->back();
        }
        else {
            return redirect()->route('penjadwalans.index'); 
        }
       
    }


    public function status_terlaksana($id){ 

            $penjadwalan = Penjadwalan::find($id);   
            $penjadwalan->status_jadwal = 1;
            $penjadwalan->save();  

        Session::flash("flash_notification", [
            "level"=>"info",
            "message"=>"Penjadwalan Berhasil Terlaksana"
        ]);
 
        return redirect()->route('penjadwalans.index');
    } 


    public function status_belum_terlaksana($id){ 

            $penjadwalan = Penjadwalan::find($id);   
            $penjadwalan->status_jadwal = 0;
            $penjadwalan->save();  

        Session::flash("flash_notification", [
            "level"=>"primary",
            "message"=>"Penjadwalan Berhasil Belum Terlaksana"
        ]);
 
        return redirect()->route('penjadwalans.index');
    } 

    public function status_batal($id){ 

            $penjadwalan = Penjadwalan::find($id);   
            $penjadwalan->status_jadwal = 2;
            $penjadwalan->save();  

        Session::flash("flash_notification", [
            "level"=>"danger",
            "message"=>"Penjadwalan Berhasil Di Batalkan"
        ]);
 
        return redirect()->route('penjadwalans.index');
    }  
     public function status_batal_dosen(Request $request){ 

            $penjadwalan = Penjadwalan::find($request->id_jadwal);   
            $penjadwalan->status_jadwal = 2;
            $penjadwalan->save();  

            print_r($penjadwalan);
            exit;


        Session::flash("flash_notification", [
            "level"=>"danger",
            "message"=>"Jadwal Berhasil Di Batalkan"
        ]);
 
        return redirect()->back();
    } 


    public function edit($id)
    {
        //
        $penjadwalans = Penjadwalan::find($id);
        $users = DB::table('users')
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id',2)
            ->pluck('name','id');

        $jadwal_dosen = DB::table('jadwal_dosens')
            ->leftJoin('users', 'users.id', '=', 'jadwal_dosens.id_dosen')
            ->where('jadwal_dosens.id_jadwal',$id)
            ->pluck('users.name','users.id'); 
            
            $dosen = Jadwal_dosen::with(['jadwal','dosen'])->where('id_jadwal',$id)->get(); 
            $data_dosen = '';
            foreach ($dosen as $dosens) { 
              $data_dosen .= ( "'".$dosens->dosen->id ."'," ); //untuk menampilkan data user yang sesuai ketika tambah
            }    
        $data_waktu = substr($penjadwalans->waktu_mulai, 0, -3) ." - ".substr($penjadwalans->waktu_selesai, 0, -3);
      
        return view('penjadwalans.edit',['users' => $users,'data_waktu' => $data_waktu])->with(compact('penjadwalans','data_dosen')); 
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
         $this->validate($request, [
            'tanggal'   => 'required',
            'data_waktu'     => 'required',
            'id_block'    => 'required|exists:master_blocks,id',
            'id_mata_kuliah'    => 'required|exists:master_mata_kuliahs,id',
            'id_ruangan'    => 'required|exists:master_ruangans,id',
            'id_user'    => 'required|exists:users,id',
            'modul'    => 'required|exists:moduls,id',
            ]); 

         $data_setting_waktu = explode("-",$request->data_waktu);

        $penjadwalans = Penjadwalan::find($id);

            $data_penjadwalan = Penjadwalan::statusRuanganEdit($request,$data_setting_waktu,$id)->count();
            if ($data_penjadwalan == 0) {
                $dosen_punya_jadwal = array();
                foreach ($request->id_user as $user_dosen) {
                 $jadwal_dosen = Jadwal_dosen::statusDosenEdit($request,$user_dosen,$data_setting_waktu,$id); 
                 $data_jadwal_dosen = $jadwal_dosen->first(); 

                    if ($jadwal_dosen->count() > 0) {
                        array_push($dosen_punya_jadwal, ['id_jadwal'=>$data_jadwal_dosen->id_jadwal,'id_dosen'=>$data_jadwal_dosen->id_dosen]);
                    }
                } 
                if (count($dosen_punya_jadwal) > 0 ) { 
                    $message = 'Tidak Bisa Menambahkan Dosen Berikut Karena Sudah Memiliki Jadwal :<ul>'; 
                        foreach ($dosen_punya_jadwal as $dosen_punya_jadwals) {  
                            $nama_dosen = User::find($dosen_punya_jadwals['id_dosen']);
                            $data_penjadwalans = Penjadwalan::find($dosen_punya_jadwals['id_jadwal']);
                            $data_ruangan =  Master_ruangan::find($data_penjadwalans->id_ruangan);
                            $data_block = Master_block::find($data_penjadwalans->id_block);
                            $data_mata_kuliah = Master_mata_kuliah::find($data_penjadwalans->id_mata_kuliah);

                            $message .= "<li><b>$nama_dosen->name</b> Memilik Jadwal Di Ruangan <b>$data_ruangan->nama_ruangan</b> Block <b>$data_block->nama_block</b>  Mata Kuliah <b>$data_mata_kuliah->nama_mata_kuliah</b> </li>";
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
                $data_ruangan =  Master_ruangan::find($request->id_ruangan);
                $data_block = Master_block::find($request->id_block);
                $data_mata_kuliah = Master_mata_kuliah::find($request->id_mata_kuliah);

                Session::flash("flash_notification", [
                    "level"=>"danger",
                    "message"=>"Ruangan $data_ruangan->nama_ruangan Sudah Di Pakai Block $data_block->nama_block Mata Kuliah $data_mata_kuliah->nama_mata_kuliah"
                    ]);
                return redirect()->back()->withInput();
            } 
      

        $penjadwalan = Penjadwalan::where('id', $id)->update([ 
            'tanggal' =>$request->tanggal,
            'waktu_mulai'=>$data_setting_waktu[0] ,
            'waktu_selesai'=>$data_setting_waktu[1] ,
            'id_block'=>$request->id_block,
            'id_mata_kuliah'=>$request->id_mata_kuliah,
            'id_modul'=>$request->modul,
            'id_ruangan'=>$request->id_ruangan]);

        Jadwal_dosen::where('id_jadwal', $id)->delete();

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
         if(!Penjadwalan::destroy($id)) 
        {
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
