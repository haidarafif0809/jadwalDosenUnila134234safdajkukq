<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Angkatan;
use Auth;
use Session;


class AngkatanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            # code...
            $angkatan = Angkatan::select(['id','kode_angkatan','nama_angkatan',]);
            return Datatables::of($angkatan)->addColumn('action', function($angkatan){
                    return view('datatable._action', [
                        'model'     => $angkatan,
                        'form_url'  => route('angkatan.destroy', $angkatan->id),
                        'edit_url'  => route('angkatan.edit', $angkatan->id),
                        'confirm_message'   => 'Yakin Mau Menghapus ' . $angkatan->nama_angkatan. '?'
                        ]);
                })->make(true);
        }
        $html = $htmlBuilder
        ->addColumn(['data' => 'kode_angkatan', 'name' => 'kode_angkatan', 'title' => 'Kode Angkatan'])
        ->addColumn(['data' => 'nama_angkatan', 'name' => 'nama_angkatan', 'title' => 'Nama Angkatan'])          
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable'=>false]);

        return view('angkatan.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('angkatan.create');
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
            'kode_angkatan'   => 'required|unique:angkatan,kode_angkatan,',
            'nama_angkatan'     => 'required|unique:angkatan,nama_angkatan,' 
            ]);

         $modul = Angkatan::create([ 
            'kode_angkatan' =>$request->kode_angkatan,
            'nama_angkatan'=>$request->nama_angkatan]);

        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Berhasil Menambah Angkatan $modul->nama_angkatan"
            ]);
        return redirect()->route('angkatan.index');
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

            $angkatan = Angkatan::find($id);
        return view('angkatan.edit')->with(compact('angkatan')); 
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
            'kode_angkatan'   => 'required|unique:angkatan,kode_angkatan,' .$id,
            'nama_angkatan'     => 'required|unique:angkatan,nama_angkatan,' .$id
            ]);

        Angkatan::where('id', $id) ->update([ 
            'kode_angkatan' =>$request->kode_angkatan,
            'nama_angkatan'=>$request->nama_angkatan]);

        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Berhasil Mengubah Modul $request->nama_angkatan"
            ]);

        return redirect()->route('angkatan.index');
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

        //   //menghapus data dengan pengecekan alert /peringatan
        // $user = User::where('id_angkatan',$id); 
 
        // if ($user->count() > 0) {
        // // menyiapkan pesan error
        // $html = 'Angkatan tidak bisa dihapus karena sudah di pakai Mahasiswa'; 
        
        // Session::flash("flash_notification", [
        //   "level"=>"danger",
        //   "message"=>$html
        // ]); 

        // return redirect()->route('angkatan.index');      
        // }
        // else{

        Angkatan::destroy($id);

        Session:: flash("flash_notification", [
            "level"=>"success",
            "message"=>"Angkatan Berhasil Di Hapus"
            ]);
        return redirect()->route('angkatan.index');
        // } 
    }
}
