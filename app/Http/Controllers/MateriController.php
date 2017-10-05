<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Materi;
use App\Penjadwalan;
use Auth;
use Session;

class MateriController extends Controller
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

            $materi = Materi::select(['id','nama_materi',]);
            return Datatables::of($materi)->addColumn('action', function($materi){
                    return view('datatable._action', [
                        'model'     => $materi,
                        'form_url'  => route('materi.destroy', $materi->id),
                        'edit_url'  => route('materi.edit', $materi->id),
                        'confirm_message'   => 'Anda Yakin Ingin Menghapus Materi ' . $materi->nama_materi. ' ?'
                        ]);
                })->make(true);
        }
        $html = $htmlBuilder
        ->addColumn(['data' => 'nama_materi', 'name' => 'nama_materi', 'title' => 'Nama Materi'])          
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable'=>false]);

        return view('materi.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('materi.create');
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
            'nama_materi'     => 'required|unique:materis,nama_materi,' 
            ]);

         $materi = Materi::create([ 
            'nama_materi'=>$request->nama_materi]);

        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Berhasil Menambah Materi $materi->nama_materi"
            ]);
        return redirect()->route('materi.index');
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
        $materi = Materi::find($id);
        return view('materi.edit')->with(compact('materi')); 
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
            'nama_materi'     => 'unique:materis,nama_materi,' .$id
        ]);

        Materi::where('id', $id) ->update([ 
            'nama_materi'=>$request->nama_materi]);

        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Berhasil Mengubah Materi $request->nama_materi"
            ]);

        return redirect()->route('materi.index');
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
        $penjadwalan = Penjadwalan::where('id_materi',$id); 
 
        if ($penjadwalan->count() > 0) {
            // menyiapkan pesan error
            $html = 'Materi Tidak Bisa Dihapus Karena Sudah Dipakai Penjadwalan'; 
        
            Session::flash("flash_notification", [
              "level"=>"danger",
              "message"=>$html
            ]); 

            return redirect()->route('materi.index');      
        }
        else{

            Materi::destroy($id);

            Session:: flash("flash_notification", [
                "level"=>"success",
                "message"=>"Materi Berhasil Di Hapus"
            ]);

            return redirect()->route('materi.index');

        }

    }
}
