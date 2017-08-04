<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Master_ruangan;
use App\Penjadwalan; 
use Auth;
use Session;

class MasterRuanganController extends Controller
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
            $master_ruangans = Master_ruangan::select(['id','kode_ruangan','nama_ruangan','lokasi_ruangan']);
            return Datatables::of($master_ruangans)->addColumn('action', function($master_ruangan){
                    return view('datatable._action', [
                        'model'     => $master_ruangan,
                        'form_url'  => route('master_ruangans.destroy', $master_ruangan->id),
                        'edit_url'  => route('master_ruangans.edit', $master_ruangan->id),
                        'confirm_message'   => 'Yakin Mau Menghapus Ruangan ' . $master_ruangan->nama_ruangan . '?'
                        ]);
                })->make(true);
        }
        $html = $htmlBuilder
        ->addColumn(['data' => 'kode_ruangan', 'name' => 'kode_ruangan', 'title' => 'Kode Ruangan'])
        ->addColumn(['data' => 'nama_ruangan', 'name' => 'nama_ruangan', 'title' => 'Nama Ruangan']) 
        ->addColumn(['data' => 'lokasi_ruangan', 'name' => 'lokasi_ruangan', 'title' => 'Lokasi Ruangan'])
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable'=>false]);

        return view('master_ruangans.index')->with(compact('html'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('master_ruangans.create');
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
            'kode_ruangan'   => 'required|unique:master_ruangans,kode_ruangan,',
            'nama_ruangan'     => 'required|unique:master_ruangans,nama_ruangan,',
            'lokasi_ruangan'    => 'required'
            ]);

         $master_ruangans = Master_ruangan::create([ 
            'kode_ruangan' =>$request->kode_ruangan,
            'nama_ruangan'=>$request->nama_ruangan,
            'lokasi_ruangan'=>$request->lokasi_ruangan]);

        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Berhasil Menambah Ruangan $master_ruangans->nama_ruangan"
            ]);
        return redirect()->route('master_ruangans.index');
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
        $master_ruangans = Master_ruangan::find($id);
        return view('master_ruangans.edit')->with(compact('master_ruangans')); 
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
            'kode_ruangan'   => 'required|unique:master_ruangans,kode_ruangan,' .$id,
            'nama_ruangan'     => 'required|unique:master_ruangans,nama_ruangan,' .$id,
            'lokasi_ruangan'    => 'required',
            ]);

        Master_ruangan::where('id', $id) ->update([ 
            'kode_ruangan' =>$request->kode_ruangan,
            'nama_ruangan'=>$request->nama_ruangan,
            'lokasi_ruangan'=>$request->lokasi_ruangan]);

        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Berhasil Mengubah Ruangan $request->nama_ruangan"
            ]);

        return redirect()->route('master_ruangans.index');
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
        //menghapus data dengan pengecekan alert /peringatan
        $penjadwalan = Penjadwalan::where('id_ruangan',$id); 
 
        if ($penjadwalan->count() > 0) {
        // menyiapkan pesan error
        $html = 'Ruangan tidak bisa dihapus karena masih memiliki Penjadwalan'; 
        
        Session::flash("flash_notification", [
          "level"=>"danger",
          "message"=>$html
        ]); 

        return redirect()->route('master_ruangans.index');      
        }
        else{

        Master_ruangan::destroy($id);
        Session:: flash("flash_notification", [
            "level"=>"danger",
            "message"=>"Ruangan Berhasil Di Hapus"
            ]);
        return redirect()->route('master_ruangans.index');
        }
    }
}
