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
            $penjadwalans = Penjadwalan::with(['block','mata_kuliah','ruangan']);
            return Datatables::of($penjadwalans)->addColumn('action', function($penjadwalan){
                    return view('datatable._action', [
                        'model'     => $penjadwalan,
                        'form_url'  => route('penjadwalans.destroy', $penjadwalan->id),
                        'edit_url'  => route('penjadwalans.edit', $penjadwalan->id),
                        'confirm_message'   => 'Yakin Mau Menghapus Jadwal ?'
                        ]);
                })
            ->addColumn('jadwal_dosen', function($jadwal){
                $jadwal_dosens = Jadwal_dosen::with(['jadwal','dosen'])->where('id_jadwal',$jadwal->id)->get(); 
                    return view('penjadwalans._action', [ 
                        'model_user'     => $jadwal_dosens,
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

        return view('penjadwalans.index')->with(compact('html'));
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
            'waktu_mulai'     => 'required',
            'waktu_selesai'     => 'required',
            'id_block'    => 'required|exists:master_blocks,id',
            'id_mata_kuliah'    => 'required|exists:master_mata_kuliahs,id',
            'id_ruangan'    => 'required|exists:master_ruangans,id',
            'id_user'    => 'required|exists:users,id'
            ]); 

            foreach ($request->id_user as $user_dosen) {
                # code...

            }



         $penjadwalan = Penjadwalan::create([ 
            'tanggal' =>$request->tanggal,
            'waktu_mulai'=>$request->waktu_mulai,
            'waktu_selesai'=>$request->waktu_selesai,
            'id_block'=>$request->id_block,
            'id_mata_kuliah'=>$request->id_mata_kuliah,
            'id_ruangan'=>$request->id_ruangan]);



            foreach ($request->id_user as $user_dosen) {
                # code...
                $jadwal_dosen = Jadwal_dosen::create([ 
                    'id_jadwal' =>$penjadwalan->id,
                    'id_dosen'=>$user_dosen,
                    ]);
                
            }

        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Berhasil Menambah Penjadwalan"
            ]);
        return redirect()->route('penjadwalans.index');
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
            
            foreach ($dosen as $dosens) { 
             $jadwal_dosen_data = $dosens->dosen->id;
            }

            $jadwal_dosens = "'1','2'"; //untuk menampilkan data user yang sesuai ketika tambah
        return view('penjadwalans.edit',['users' => $users,'jadwal_dosen_data' => $jadwal_dosen_data])->with(compact('penjadwalans')); 
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
            'waktu_mulai'     => 'required',
            'waktu_selesai'     => 'required',
            'id_block'    => 'required|exists:master_blocks,id',
            'id_mata_kuliah'    => 'required|exists:master_mata_kuliahs,id',
            'id_ruangan'    => 'required|exists:master_ruangans,id',
            'id_user'    => 'required|exists:users,id'
            ]); 

        Penjadwalan::where('id', $id) ->update([ 
            'tanggal' =>$request->tanggal,
            'waktu_mulai'=>$request->waktu_mulai,
            'waktu_selesai'=>$request->waktu_selesai,
            'id_block'=>$request->id_block,
            'id_mata_kuliah'=>$request->id_mata_kuliah,
            'id_ruangan'=>$request->id_ruangan]);

        Jadwal_dosen::where('id_jadwal', $id)->delete();

            foreach ($request->id_user as $user_dosen) {
                # code...
                $jadwal_dosen = Jadwal_dosen::create([ 
                    'id_jadwal' =>$id,
                    'id_dosen'=>$user_dosen,
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
            "level"=>"success",
            "message"=>"Penjadwalan berhasil dihapus"
            ]);
        return redirect()->route('penjadwalans.index');
            }
    }
}
