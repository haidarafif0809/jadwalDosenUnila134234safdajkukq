<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Master_block; //Modal
use App\Penjadwalan; 
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
            })->make(true);
        }
        $html = $htmlBuilder
        ->addColumn(['data' => 'kode_block', 'name' => 'kode_block', 'title' => 'Kode Block'])
        ->addColumn(['data' => 'nama_block', 'name' => 'nama_block', 'title' => 'Nama Block'])
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
