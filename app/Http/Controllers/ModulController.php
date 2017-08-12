<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Modul;
use App\ModulBlok;
use Auth;
use Session;

class ModulController extends Controller
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
            $master_modul = Modul::select(['id','kode_modul','nama_modul',]);
            return Datatables::of($master_modul)->addColumn('action', function($master_modul){
                    return view('datatable._action', [
                        'model'     => $master_modul,
                        'form_url'  => route('modul.destroy', $master_modul->id),
                        'edit_url'  => route('modul.edit', $master_modul->id),
                        'confirm_message'   => 'Yakin Mau Menghapus Mata Kuliah ' . $master_modul->nama_modul. '?'
                        ]);
                })->make(true);
        }
        $html = $htmlBuilder
        ->addColumn(['data' => 'kode_modul', 'name' => 'kode_modul', 'title' => 'Kode Modul'])
        ->addColumn(['data' => 'nama_modul', 'name' => 'nama_modul', 'title' => 'Nama Modul'])          
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable'=>false]);

        return view('master_modul.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

         return view('master_modul.create');
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
            'kode_modul'   => 'required|unique:moduls,kode_modul,',
            'nama_modul'     => 'required|unique:moduls,nama_modul,' 
            ]);

         $modul = Modul::create([ 
            'kode_modul' =>$request->kode_modul,
            'nama_modul'=>$request->nama_modul]);

        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Berhasil Menambah Modul $modul->nama_modul"
            ]);
        return redirect()->route('modul.index');
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
           $modul = Modul::find($id);
        return view('master_modul.edit')->with(compact('modul')); 
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

        //
         $this->validate($request, [
            'kode_modul'   => 'required|unique:moduls,kode_modul,' .$id,
            'nama_modul'     => 'required|unique:moduls,nama_modul,' .$id
            ]);

        Modul::where('id', $id) ->update([ 
            'kode_modul' =>$request->kode_modul,
            'nama_modul'=>$request->nama_modul]);

        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Berhasil Mengubah Modul $request->nama_modul"
            ]);

        return redirect()->route('modul.index');
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
        $modul_blok = ModulBlok::where('id_modul',$id); 
 
        if ($modul_blok->count() > 0) {
        // menyiapkan pesan error
        $html = 'Modul tidak bisa dihapus karena sudah di pakai Block'; 
        
        Session::flash("flash_notification", [
          "level"=>"danger",
          "message"=>$html
        ]); 

        return redirect()->route('modul.index');      
        }
        else{

        Modul::destroy($id);

        Session:: flash("flash_notification", [
            "level"=>"success",
            "message"=>"Modul Berhasil Di Hapus"
            ]);
        return redirect()->route('modul.index');
        } 
    }
}
