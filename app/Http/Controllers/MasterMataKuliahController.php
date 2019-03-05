<?php

namespace App\Http\Controllers;

use App\Master_mata_kuliah;
use App\Penjadwalan;
use Illuminate\Http\Request;
use Session;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;

class MasterMataKuliahController extends Controller
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
            $master_mata_kuliahs = Master_mata_kuliah::select(['id', 'kode_mata_kuliah', 'nama_mata_kuliah']);
            return Datatables::of($master_mata_kuliahs)->addColumn('action', function ($master_mata_kuliah) {
                return view('datatable._action', [
                    'model'           => $master_mata_kuliah,
                    'form_url'        => route('master_mata_kuliahs.destroy', $master_mata_kuliah->id),
                    'edit_url'        => route('master_mata_kuliahs.edit', $master_mata_kuliah->id),
                    'confirm_message' => 'Yakin Mau Menghapus Mata Kuliah ' . $master_mata_kuliah->nama_mata_kuliah . '?',
                ]);
            })->make(true);
        }
        $html = $htmlBuilder
            ->addColumn(['data' => 'kode_mata_kuliah', 'name' => 'kode_mata_kuliah', 'title' => 'Kode Mata Kuliah'])
            ->addColumn(['data' => 'nama_mata_kuliah', 'name' => 'nama_mata_kuliah', 'title' => 'Nama Mata Kuliah'])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable' => false]);

        return view('master_mata_kuliahs.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('master_mata_kuliahs.create');
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
            'kode_mata_kuliah' => 'required|unique:master_mata_kuliahs,kode_mata_kuliah,',
            'nama_mata_kuliah' => 'required|unique:master_mata_kuliahs,nama_mata_kuliah,',
        ]);

        $master_mata_kuliahs = Master_mata_kuliah::create([
            'kode_mata_kuliah' => $request->kode_mata_kuliah,
            'nama_mata_kuliah' => $request->nama_mata_kuliah]);

        Session::flash("flash_notification", [
            "level"   => "success",
            "message" => "Berhasil Menambah Mata Kuliah $master_mata_kuliahs->nama_mata_kuliah",
        ]);
        return redirect()->route('master_mata_kuliahs.index');
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
        $master_mata_kuliahs = Master_mata_kuliah::find($id);
        return view('master_mata_kuliahs.edit')->with(compact('master_mata_kuliahs'));
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
            'kode_mata_kuliah' => 'required|unique:master_mata_kuliahs,kode_mata_kuliah,' . $id,
            'nama_mata_kuliah' => 'required|unique:master_mata_kuliahs,nama_mata_kuliah,' . $id,
        ]);

        Master_mata_kuliah::where('id', $id)->update([
            'kode_mata_kuliah' => $request->kode_mata_kuliah,
            'nama_mata_kuliah' => $request->nama_mata_kuliah]);

        Session::flash("flash_notification", [
            "level"   => "success",
            "message" => "Berhasil Mengubah Mata Kuliah $request->nama_mata_kuliah",
        ]);

        return redirect()->route('master_mata_kuliahs.index');
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
        $penjadwalan = Penjadwalan::where('id_mata_kuliah', $id);

        if ($penjadwalan->count() > 0) {
            // menyiapkan pesan error
            $html = 'Mata Kuliah tidak bisa dihapus karena masih memiliki Penjadwalan';

            Session::flash("flash_notification", [
                "level"   => "danger",
                "message" => $html,
            ]);

            return redirect()->route('master_mata_kuliahs.index');
        } else {

            Master_mata_kuliah::destroy($id);
            Session::flash("flash_notification", [
                "level"   => "danger",
                "message" => "Mata Kuliah Berhasil Di Hapus",
            ]);
            return redirect()->route('master_mata_kuliahs.index');
        }
    }
}
