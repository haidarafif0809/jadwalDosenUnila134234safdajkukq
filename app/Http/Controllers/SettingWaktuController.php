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
            $settingwaktu = SettingWaktu::select(['id','waktu_mulai','waktu_selesai']);
            return Datatables::of($settingwaktu)->addColumn('action', function($settingwaktu){
                return view('datatable._action', [
                    'model'             => $settingwaktu,
                    'form_url'          => route('settingwaktu.destroy', $settingwaktu->id),
                    'edit_url'          => route('settingwaktu.edit', $settingwaktu->id),
                    'confirm_message'   => 'Apakah Anda Yakin Ingin Menghapus Waktu ' .$settingwaktu->waktu_mulai. ' - ' .$settingwaktu->waktu_selesai. ' ?'
                ]);
            })->make(true);
        }
        $html = $htmlBuilder
        ->addColumn(['data' => 'waktu_mulai', 'name' => 'waktu_mulai', 'title' => 'Waktu Mulai']) 
        ->addColumn(['data' => 'waktu_selesai', 'name' => 'waktu_selesai', 'title' => 'Waktu Selesai']) 
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
            'waktu_mulai' => 'required|unique:setting_waktus,waktu_mulai',
            'waktu_selesai' => 'required|unique:setting_waktus,waktu_selesai'
        ]);
    
        $settingwaktu = SettingWaktu::create([
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai
        ]);

        Session::flash("flash_notification", [
            "level"     => "success",
            "message"   => "Berhasil Menambah Waktu Mulai $request->waktu_mulai Waktu Selesai $request->waktu_selesai"
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
            'waktu_mulai'   => 'required|unique:setting_waktus,waktu_mulai,' .$id ,
            'waktu_selesai'   => 'required|unique:setting_waktus,waktu_selesai,' .$id
            ]);

        SettingWaktu::where('id', $id)->update([ 
            'waktu_mulai' =>$request->waktu_mulai,
            'waktu_selesai' =>$request->waktu_selesai
            ]);

        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Berhasil Mengubah Waktu Mulai $request->waktu_mulai Waktu Selesai $request->waktu_selesai"
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
