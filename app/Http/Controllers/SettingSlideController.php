<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\SettingSlide;
use Session;

class SettingSlideController extends Controller
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

            $setting_slide = SettingSlide::select('id','slide','judul_slide'); 
            return Datatables::of($setting_slide)
            ->addColumn('action', function($setting_slide){
                    return view('datatable._action', [
                        'model'     => $setting_slide,
                        'form_url'  => route('setting_slide.destroy', $setting_slide->id),
                        'edit_url'  => route('setting_slide.edit', $setting_slide->id),
                        'confirm_message'   => 'Yakin Mau Menghapus Slide ' . $setting_slide->judul_slide. '?'
                        ]);
                })
            //UNTUK MENAMPILKAN FOTO SLIDE DI DATATABLE
            ->addColumn('slide',function($slide){
                return view('setting_slide._slide', [
                        'slide'=> $slide
                         ]);
                })->make(true);
            }

            $html = $htmlBuilder
            ->addColumn(['data' => 'slide', 'name'=>'slide', 'title'=>'Foto Slide'])
            ->addColumn(['data' => 'judul_slide', 'name'=>'judul_slide', 'title'=>'Judul Slide']) 
            ->addColumn(['data' => 'action', 'name'=>'action', 'title'=>'', 'orderable'=>false, 'searchable'=>false]);

            return view('setting_slide.index')->with(compact('html'));
    }

 
    public function create()
    {
         return view('setting_slide.create');

    }
 
    public function store(Request $request)
    {
           $this->validate($request, [ 
                'slide' => 'image|max:2048', 
                'judul_slide' => 'max:191',    
                ]); 
        //PROSES MEMBUAT DATA SLIDE   
        $setting_slide =  SettingSlide::create();
        //MENAMBAHKAN NAMA JUDUL SLIDE
        $setting_slide->judul_slide = $request->judul_slide; 
        //PROSES MENG UPLOAD FOTO SLIDE
         // isi field cover jika ada cover yang diupload
        if ($request->hasFile('slide')) {
            // Mengambil file yang diupload
            $uploaded_slide = $request->file('slide');
            // mengambil extension file
            $extension = $uploaded_slide->getClientOriginalExtension();
            // membuat nama file random berikut extension
            $filename = md5(microtime()) . '.' . $extension;
            // menyimpan cover ke folder public/img
            $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'img';
            $uploaded_slide->move($destinationPath, $filename);
            // mengisi field cover di book dengan filename yang baru dibuat
            $setting_slide->slide = $filename;
            $setting_slide->save();
        }

        Session::flash("flash_notification", [
        "level"=>"success",
        "message"=>"Berhasil Menambah Slide"
        ]);
 
        return redirect()->route('setting_slide.index'); 
    }

    public function edit($id)
    { 
        $setting_slide = SettingSlide::find($id);
         return view('setting_slide.edit')->with(compact('setting_slide'));
    }
 
    public function update(Request $request, $id)
    { 
           $this->validate($request, [ 
                'slide' => 'image|max:2048', 
                'judul_slide' => 'max:191',    
                ]); 
           //PROSES UPDATE SETTING SLIDE  
            $setting_slide = SettingSlide::find($id);
            //PROSES MENGUBAH NAMA JUDUL SLIDE
            $setting_slide->judul_slide = $request->judul_slide; 
            $setting_slide->update();
            //PROSES MENG UPDATE FOTO SLIDE
        if ($request->hasFile('slide')) {
            $filename = null;
            // Mengambil file yang diupload
            $uploaded_slide = $request->file('slide');
            // mengambil extension file
            $extension = $uploaded_slide->getClientOriginalExtension();
            // membuat nama file random berikut extension
            $filename = md5(microtime()) . '.' . $extension;
            // menyimpan cover ke folder public/img
            $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'img';
            $uploaded_slide->move($destinationPath, $filename);

            //PROSES MENGHAPUS FOTO SLIDE APA BILA SLIDE DI UBAH
            // hapus cover lama, jika ada
            if ($setting_slide->slide) {
                $old_slide = $setting_slide->slide;
                $filepath = public_path() . DIRECTORY_SEPARATOR . 'img'
                . DIRECTORY_SEPARATOR . $setting_slide->slide;
                try {
                File::delete($filepath);
                } catch (FileNotFoundException $e) {
                // File sudah dihapus/tidak ada
                }
            }
            // mengisi field cover di book dengan filename yang baru dibuat
            $setting_slide->slide = $filename;
            $setting_slide->save();
        }
 

          Session::flash("flash_notification", [
        "level"=>"success",
        "message"=>"Berhasil Mengubah Slide"
        ]);


        return redirect()->route('setting_slide.index');
    }
 
    public function destroy($id)
    {       
        //MENGAMBIL DATA SETTING SLIDE SESUAI ID YANG DI PANGGIL
            $setting_slide = SettingSlide::find($id);
            //PROSES HAPUS FOTO SLIDE
            if ($setting_slide->slide) {

            $old_slide = $setting_slide->slide;
            $filepath = public_path() . DIRECTORY_SEPARATOR . 'img'
            . DIRECTORY_SEPARATOR . $setting_slide->slide;
            
                try {
                File::delete($filepath);
                } catch (FileNotFoundException $e) {
                // File sudah dihapus/tidak ada
                }

            }
            //PROSES HAPUS SLIDE
            $setting_slide->delete();

            Session::flash("flash_notification", [
                "level"     => "danger",
                "message"   => "Slide Berhasil Di Hapus"
            ]);
        return redirect()->route('setting_slide.index'); 
    }
}
