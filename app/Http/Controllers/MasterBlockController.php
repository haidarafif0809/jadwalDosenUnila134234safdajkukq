<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Master_block; //Modal
use App\Penjadwalan; 
use App\ModulBlok; 
use App\MahasiswaBlock; 
use Auth;
use Session;

class MasterBlockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            $master_blocks = Master_block::select(['id', 'kode_block', 'nama_block']);
            return Datatables::of($master_blocks)->addColumn('action', function($master_blocks){
                return view('datatable._action', [
                    'model'             => $master_blocks,
                    'form_url'          => route('master_blocks.destroy', $master_blocks->id),
                    'edit_url'          => route('master_blocks.edit', $master_blocks->id),
                    'confirm_message'   => 'Apakah Anda Yakin Ingin Menghapus Block ' .$master_blocks->nama_block. '?'
                ]);
            })->addColumn('modul', function($master_blocks){
                return '<a class="btn btn-default" href="'.route('master_blocks.modul',$master_blocks->id).'">Lihat Modul</a>';
            })->addColumn('mahasiswa', function($master_blocks){
                return '<a class="btn btn-default" href="'.route('master_blocks.mahasiswa',$master_blocks->id).'">Lihat Mahasiswa</a>';
            })->make(true);
        }
        $html = $htmlBuilder
        ->addColumn(['data' => 'kode_block', 'name' => 'kode_block', 'title' => 'Kode Block'])
        ->addColumn(['data' => 'nama_block', 'name' => 'nama_block', 'title' => 'Nama Block'])
        ->addColumn(['data' => 'modul', 'name' => 'modul', 'title' => 'Modul','orderable' => false, 'searchable' => false]) 
         ->addColumn(['data' => 'mahasiswa', 'name' => 'mahasiswa', 'title' => 'Mahasiswa','orderable' => false, 'searchable' => false])
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable' => false]);

        return view('master_blocks.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('master_blocks.create');
    }

     public function createModul(Request $request, Builder $htmlBuilder,$id)
    {

          //datatable 
        if ($request->ajax()) {
            $modul = ModulBlok::with('modul')->where('id_blok',$id);
            return Datatables::of($modul)->addColumn('action', function($modul){
                return view('datatable._hapus', [
                    'model'             => $modul,
                    'form_url'          => route('modul_block.destroy', $modul->id_modul_blok),
                  
                    'confirm_message'   => 'Apakah Anda Yakin Ingin Menghapus Mahasiswa ' .$modul->modul->nama_modul. '?'
                ]);
            })->addColumn('tanggal', function($modul){
                return $modul->dari_tanggal." - > ". $modul->sampai_tanggal;
            })->addColumn('lihat_jadwal', function($modul){
                return "<a class='btn btn-default' href='". route('modul.jadwal',['id_modul' =>$modul->id_modul_blok,'id_block'=> $modul->id_blok]). "'>Jadwal</a>";
            })->make(true);
        }

        $html = $htmlBuilder
        ->addColumn(['data' => 'modul.nama_modul', 'name' => 'modul.nama_modul', 'title' => 'Mahasiswa', 'orderable' => false])
         ->addColumn(['data' => 'tanggal', 'name' => 'tanggal', 'title' => 'Periode', 'orderable' => false])
         ->addColumn(['data' => 'lihat_jadwal', 'name' => 'lihat_jadwal', 'title' => 'Jadwal', 'orderable' => false, 'searchable' => false])
           ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable' => false]) ;

        return view('master_blocks.create_modul',['id' =>$id])->with(compact('html'));
    }   

    public function lihat_jadwal_permodul ($id_modul,$id_block){
        


        $modul = ModulBlok::with('modul','block')->where('id_blok',$id_block)->where('id_modul_blok',$id_modul)->first();



        $penjadwalan = Penjadwalan::with(['mata_kuliah','block','ruangan'])->where('id_block',$id_block)->where('tanggal','>=',$modul->dari_tanggal)->where('tanggal','<=',$modul->sampai_tanggal)->get();
        $tanggal_akhir = date('Y-m-d', strtotime($modul->sampai_tanggal .' +2 day'));


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
                  

                array_push($jadwal_senin, ['id_jadwal'=> $penjadwalans->id ,'waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $penjadwalans->mata_kuliah->nama_mata_kuliah]);
                
                    break; 
                 case '2':
                 

                array_push($jadwal_selasa, ['id_jadwal'=> $penjadwalans->id ,'waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $penjadwalans->mata_kuliah->nama_mata_kuliah]);

                    break;    
                 case '3':
                  
                array_push($jadwal_rabu, ['id_jadwal'=> $penjadwalans->id ,'waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $penjadwalans->mata_kuliah->nama_mata_kuliah]);

                    break;    
                 case '4':
                   
                array_push($jadwal_kamis, ['id_jadwal'=> $penjadwalans->id ,'waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $penjadwalans->mata_kuliah->nama_mata_kuliah]);

                    break;    
                 case '5':
                  
                array_push($jadwal_jumat, ['id_jadwal'=> $penjadwalans->id ,'waktu_mulai'=> $penjadwalans->waktu_mulai,'waktu_selesai'=> $penjadwalans->waktu_selesai,'nama_mata_kuliah'=> $penjadwalans->mata_kuliah->nama_mata_kuliah]);

                    break;
                
                default:
                    
                    break;
            }

        }
    //dosen 
     $users = DB::table('users')
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id',2)
            ->pluck('name','id');
        $asal_input = 1;
        
        return view('master_modul.index_jadwal',['jadwal_senin'=> $jadwal_senin,'jadwal_selasa' => $jadwal_selasa,'jadwal_rabu' => $jadwal_rabu,'jadwal_kamis' => $jadwal_kamis,'jadwal_jumat' => $jadwal_jumat,'modul' => $modul,'users' => $users,'asal_input' => $asal_input,'tanggal_akhir' => $tanggal_akhir]);
    }

  public function createMahasiswa(Request $request, Builder $htmlBuilder,$id)
    {

        $mahasiswa = DB::table('users')
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id',3)
            ->pluck('name','id');
        $block = Master_block::find($id);

        //datatable 
        if ($request->ajax()) {
            $mahasiswa_block = MahasiswaBlock::with('mahasiswa')->where('id_block',$id);
            return Datatables::of($mahasiswa_block)->addColumn('action', function($mahasiswa_block){
                return view('datatable._hapus', [
                    'model'             => $mahasiswa_block,
                    'form_url'          => route('mahasiswa_block.destroy', $mahasiswa_block->id),
                  
                    'confirm_message'   => 'Apakah Anda Yakin Ingin Menghapus Mahasiswa ' .$mahasiswa_block->mahasiswa->name. '?'
                ]);
            })->make(true);
        }

        $html = $htmlBuilder
        ->addColumn(['data' => 'mahasiswa.name', 'name' => 'mahasiswa.name', 'title' => 'Mahasiswa', 'orderable' => false])
           ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable' => false]);



        return view('master_blocks.create_mahasiswa',['id' =>$id,'mahasiswa' => $mahasiswa,'block' => $block])->with(compact('html'));
    }   

    public function hapus_mahasiswa_block($id){
        MahasiswaBlock::destroy($id);

          Session::flash("flash_notification", [
                "level"     => "danger",
                "message"   => "Mahasiswa Berhasil Di Hapus"
            ]);
        return redirect()->back();

    }  
     public function hapus_modul_block($id){
        ModulBlok::destroy($id);

          Session::flash("flash_notification", [
                "level"     => "danger",
                "message"   => "Modul Berhasil Di Hapus"
            ]);
        return redirect()->back();

    }

    public function proses_kait_modul_blok(Request $request,$id)
    {


        function tanggal_mysql($tanggal){
                $date= date_create($tanggal);
                return $date_format =  date_format($date,"Y-m-d");    
         }
         $this->validate($request, [
       
            'dari_tanggal'     => 'required|date',
            'modul'    => 'required|exists:moduls,id|unique:modul_bloks,id_modul,NULL,id_modul_blok,id_blok,'.$id
            ]);   

         $sampai_tanggal = date('d-m-Y', strtotime($request->dari_tanggal .' +4 day'));

         ModulBlok::create(['id_modul'=> $request->modul,'id_blok' => $id,'dari_tanggal' => tanggal_mysql($request->dari_tanggal),'sampai_tanggal' => tanggal_mysql($sampai_tanggal)]);

        Session::flash("flash_notification", [
            "level"     => "success",
            "message"   => "Berhasil Mengaitkan Modul ke blok"
        ]);

         return redirect()->back();

        
    
    }  
    public function proses_kait_mahasiswa_blok(Request $request,$id)
    {

         $this->validate($request, [
       
            'mahasiswa'    => 'required|unique:mahasiswa_block,id_mahasiswa,NULL,id,id_block,'.$id
            ]);   

         MahasiswaBlock::create(['id_mahasiswa'=> $request->mahasiswa,'id_block' => $id]);

        Session::flash("flash_notification", [
            "level"     => "success",
            "message"   => "Berhasil Mengaitkan Mahasiswa ke block"
        ]);

         return redirect()->back();

        
    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'kode_block' => 'required|unique:master_blocks,kode_block,',
            'nama_block' => 'required|unique:master_blocks,nama_block'
        ]);
    
        $master_blocks = Master_block::create([
            'kode_block' => $request->kode_block,
            'nama_block' => $request->nama_block
        ]);

        Session::flash("flash_notification", [
            "level"     => "success",
            "message"   => "Berhasil Menambah Block $master_blocks->nama_block"
        ]);

        return redirect()->route('master_blocks.index');
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
        $master_blocks = Master_block::find($id);
        return view('master_blocks.edit')->with(compact('master_blocks'));
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
        $this->validate($request, [
            'kode_block' => 'required|unique:master_blocks,kode_block,' .$id,
            'nama_block' => 'required|unique:master_blocks,nama_block,' .$id
        ]);

        Master_block::where('id', $id)->update([            
            'kode_block' => $request->kode_block,
            'nama_block' => $request->nama_block
        ]);

        Session::flash("flash_notification", [
            "level"     => "success",
            "message"   => "Berhasil Mengubah Block $request->nama_block"
        ]);

        return redirect()->route('master_blocks.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //menghapus data dengan pengecekan alert /peringatan
        $penjadwalan = Penjadwalan::where('id_block',$id); 
 
        if ($penjadwalan->count() > 0) {
        // menyiapkan pesan error
        $html = 'Block tidak bisa dihapus karena masih memiliki Penjadwalan'; 
        
        Session::flash("flash_notification", [
          "level"=>"danger",
          "message"=>$html
        ]); 

        return redirect()->route('master_blocks.index');      
        }
        else{

        Master_block::destroy($id);
            Session::flash("flash_notification", [
                "level"     => "danger",
                "message"   => "Master Data Block Berhasil Di Hapus"
            ]);
        return redirect()->route('master_blocks.index');
        }
    }
}
