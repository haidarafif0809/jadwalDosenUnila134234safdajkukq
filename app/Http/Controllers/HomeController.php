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

        $user_otoritas = Auth::user()->roles->first()->name;
        switch ($user_otoritas) {
            case 'admin':
                
              
                return view('home');

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
                break;
            
            default:
                # code...
                break;
        }

    }  

    public function jadwal_kuliah_dosen(Request $request, Builder $htmlBuilder){

           if ($request->ajax()) {
            # code...


        $dosen = Auth::user()->id;


            if (isset($request->dari_tanggal)) {
                       $penjadwalans = Jadwal_dosen::with(['block','mata_kuliah','ruangan'])->where('id_dosen',$dosen)->where('tanggal' ,'>=',$request->dari_tanggal)->where('tanggal','<=',$request->sampai_tanggal)->groupBy('id_jadwal');
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
        ->addColumn(['data' => 'jadwal_dosen', 'name' => 'jadwal_dosen', 'title' => 'Dosen', 'orderable' => false, 'searchable'=>false]);


        return view('dosen.index')->with(compact('html'));

    }

    public function jadwal_kuliah_mahasiswa(){
        $mahasiswa = Auth::user()->id;

       $block = DB::table('mahasiswa_block')
            ->leftJoin('master_blocks', 'mahasiswa_block.id_block', '=', 'master_blocks.id')
            ->where('mahasiswa_block.id_mahasiswa',$mahasiswa)
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
                  $isi_event = "
                <p>
                Waktu : ".$penjadwalans->tanggal." ". $penjadwalans->waktu_mulai ."-". $penjadwalans->waktu_selesai ." <br>
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
            
            $timestamp = strtotime($penjadwalans->tanggal);
            $day = date('w', $timestamp);


            switch ($day) {
                case '1':
                    # code...

                array_push($jadwal_senin, ['id_jadwal'=> $penjadwalans->id ,'waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $penjadwalans->mata_kuliah->nama_mata_kuliah]);
                
                    break; 
                 case '2':
                    # code...

                array_push($jadwal_selasa, ['id_jadwal'=> $penjadwalans->id ,'waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $penjadwalans->mata_kuliah->nama_mata_kuliah]);

                    break;    
                 case '3':
                    # code...
                array_push($jadwal_rabu, ['id_jadwal'=> $penjadwalans->id ,'waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $penjadwalans->mata_kuliah->nama_mata_kuliah]);

                    break;    
                 case '4':
                    # code...
                array_push($jadwal_kamis, ['id_jadwal'=> $penjadwalans->id ,'waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $penjadwalans->mata_kuliah->nama_mata_kuliah]);

                    break;    
                 case '5':
                    # code...
                array_push($jadwal_jumat, ['id_jadwal'=> $penjadwalans->id ,'waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $penjadwalans->mata_kuliah->nama_mata_kuliah]);

                    break;
                
                default:
                    # code...
                    break;
            }

        }

       $mahasiswa = Auth::user()->id;

       $block = DB::table('mahasiswa_block')
            ->leftJoin('master_blocks', 'mahasiswa_block.id_block', '=', 'master_blocks.id')
            ->where('mahasiswa_block.id_mahasiswa',$mahasiswa)
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
            
            $timestamp = strtotime($penjadwalans->tanggal);
            $day = date('w', $timestamp);


            switch ($day) {
                case '1':
                    # code...
                array_push($jadwal_senin, ['waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $penjadwalans->mata_kuliah->nama_mata_kuliah]);
                
                    break; 
                 case '2':
                    # code...
                array_push($jadwal_selasa, ['waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $penjadwalans->mata_kuliah->nama_mata_kuliah]);

                    break;    
                 case '3':
                    # code...
                array_push($jadwal_rabu, ['waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $penjadwalans->mata_kuliah->nama_mata_kuliah]);

                    break;    
                 case '4':
                    # code...
                array_push($jadwal_kamis, ['waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $penjadwalans->mata_kuliah->nama_mata_kuliah]);

                    break;    
                 case '5':
                    # code...
                array_push($jadwal_jumat, ['waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $penjadwalans->mata_kuliah->nama_mata_kuliah]);

                    break;
                
                default:
                    # code...
                    break;
            }

        }
   

        return view('contoh_jadwal_kuliah',['jadwal_senin'=> $jadwal_senin,'jadwal_selasa' => $jadwal_selasa,'jadwal_rabu' => $jadwal_rabu,'jadwal_kamis' => $jadwal_kamis,'jadwal_jumat' => $jadwal_jumat,'modul' => $modul]);
    }
}
