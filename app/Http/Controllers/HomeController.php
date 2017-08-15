<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Penjadwalan;
use App\ModulBlok;
use App\Master_block;
use Auth;
use Illuminate\Support\Facades\DB;

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
    public function index()
    {

        $user_otoritas = Auth::user()->roles->first()->name;
        switch ($user_otoritas) {
            case 'admin':
                
              
                return view('home');

                break;  
            case 'dosen':
                # code...

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

    public function proses_jadwal_mahasiswa(Request $request){

        $this->validate($request, [
            'block'     => 'required',
            'modul'     => 'required' ]);  

        $modul = ModulBlok::with('modul')->where('id_blok',$request->block)->where('id_modul',$request->modul)->first();

        $penjadwalan = Penjadwalan::with('mata_kuliah')->where('id_block',$request->block)->where('tanggal','>=',$modul->dari_tanggal)->where('tanggal','<=',$modul->sampai_tanggal)->get();

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
