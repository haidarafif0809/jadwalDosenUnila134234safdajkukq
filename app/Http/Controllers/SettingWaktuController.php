<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\SettingWaktu; //Modal  
use Session;


class SettingWaktuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            $settingwaktu = SettingWaktu::select(['id','waktu']);
            return Datatables::of($settingwaktu)->addColumn('action', function($settingwaktu){
                return view('datatable._action', [
                    'model'             => $settingwaktu,
                    'form_url'          => route('settingwaktu.destroy', $settingwaktu->id),
                    'edit_url'          => route('settingwaktu.edit', $settingwaktu->id),
                    'confirm_message'   => 'Apakah Anda Yakin Ingin Menghapus Waktu ' .$settingwaktu->waktu. ' ?'
                ]);
            })->make(true);
        }
        $html = $htmlBuilder
        ->addColumn(['data' => 'waktu', 'name' => 'waktu', 'title' => 'Waktu']) 
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable' => false]);

        return view('settingwaktu.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('settingwaktu.create');
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
            'waktu' => 'required|unique:setting_waktus,waktu,'
        ]);
    
        $settingwaktu = SettingWaktu::create([
            'waktu' => $request->waktu
        ]);

        Session::flash("flash_notification", [
            "level"     => "success",
            "message"   => "Berhasil Menambah Waktu $settingwaktu->waktu"
        ]);

        return redirect()->route('settingwaktu.index');
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
        $settingwaktu = SettingWaktu::find($id);
        return view('settingwaktu.edit')->with(compact('settingwaktu'));
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
            'waktu'   => 'required|unique:setting_waktus,waktu,' .$id
            ]);

        SettingWaktu::where('id', $id)->update([ 
            'waktu' =>$request->waktu]);

        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Berhasil Mengubah Waktu $request->waktu"
            ]);

        return redirect()->route('settingwaktu.index');
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
         if(!SettingWaktu::destroy($id)) 
        {
            return redirect()->back();
        }
        else{ 
            Session:: flash("flash_notification", [
            "level"=>"danger",
            "message"=>"Waktu berhasil Di Hapus"
            ]);

            return redirect()->route('settingwaktu.index');
        }

    }
}
