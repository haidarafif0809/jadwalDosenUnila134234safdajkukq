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

            $setting_slide = SettingSlide::select('id','slide_1','slide_2','slide_3','judul_slide_1','judul_slide_2','judul_slide_3');

            return Datatables::of($setting_slide)
            ->addColumn('action', function($setting_slide){
                    return view('setting_slide._action', [ 
                    'edit_url'=> route('setting_slide.edit', $setting_slide->id) 
                    ]);
                })
            ->addColumn('slide_1',function($slide_1){
                return view('setting_slide.slide_1', [
                        'slide_1'=> $slide_1
                         ]);
                })
            ->addColumn('slide_2',function($slide_2){
                return view('setting_slide.slide_2', [
                        'slide_2'=> $slide_2
                         ]);
                })
            ->addColumn('slide_3',function($slide_3){
                return view('setting_slide.slide_3', [
                        'slide_3'=> $slide_3
                         ]);
                })->make(true);
            }

            $html = $htmlBuilder
            ->addColumn(['data' => 'slide_1', 'name'=>'slide_1', 'title'=>'Slide Pertama'])
            ->addColumn(['data' => 'judul_slide_1', 'name'=>'judul_slide_1', 'title'=>'Judul Slide Pertama'])
            ->addColumn(['data' => 'slide_2', 'name'=>'slide_2', 'title'=>'Slide Kedua'])
            ->addColumn(['data' => 'judul_slide_2', 'name'=>'judul_slide_2', 'title'=>'Judul Slide Kedua'])
            ->addColumn(['data' => 'slide_3', 'name'=>'slide_3', 'title'=>'Slide Ketiga'])
            ->addColumn(['data' => 'judul_slide_3', 'name'=>'judul_slide_3', 'title'=>'Judul Slide Ketiga'])
            ->addColumn(['data' => 'action', 'name'=>'action', 'title'=>'', 'orderable'=>false, 'searchable'=>false]);

            return view('setting_slide.index')->with(compact('html'));
    }

 
    public function edit($id)
    {
        //
        $setting_slide = SettingSlide::find($id);
         return view('setting_slide.edit')->with(compact('setting_slide'));
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
                'slide_1' => 'image|max:2048',
                'slide_2' => 'image|max:2048',
                'slide_3' => 'image|max:2048',
                'judul_slide_1' => 'max:191',   
                'judul_slide_2' => 'max:191',
                'judul_slide_3' => 'max:191',    
                ]); 

             SettingSlide::where('id', $id) ->update([ 
            'judul_slide_1' =>$request->judul_slide_1,
            'judul_slide_2' =>$request->judul_slide_2, 
            'judul_slide_3' =>$request->judul_slide_3,  
            ]);

            $setting_slide = SettingSlide::find($id);
            $setting_slide->update();

        if ($request->hasFile('slide_1')) {
            $filename = null;
            // Mengambil file yang diupload
            $uploaded_slide_1 = $request->file('slide_1');
            // mengambil extension file
            $extension = $uploaded_slide_1->getClientOriginalExtension();
            // membuat nama file random berikut extension
            $filename = md5(microtime()) . '.' . $extension;
            // menyimpan cover ke folder public/img
            $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'img';
            $uploaded_slide_1->move($destinationPath, $filename);


            // hapus cover lama, jika ada
            if ($setting_slide->slide_1) {
            $old_slide_1 = $setting_slide->slide_1;
            $filepath = public_path() . DIRECTORY_SEPARATOR . 'img'
            . DIRECTORY_SEPARATOR . $setting_slide->slide_1;
            try {
            File::delete($filepath);
            } catch (FileNotFoundException $e) {
            // File sudah dihapus/tidak ada
            }
            }
            // mengisi field cover di book dengan filename yang baru dibuat
            $setting_slide->slide_1 = $filename;
            $setting_slide->save();
        }

        if ($request->hasFile('slide_2')) {
            $filename = null;
            // Mengambil file yang diupload
            $uploaded_slide_2 = $request->file('slide_2');
            // mengambil extension file
            $extension = $uploaded_slide_2->getClientOriginalExtension();
            // membuat nama file random berikut extension
            $filename = md5(microtime()) . '.' . $extension;
            // menyimpan cover ke folder public/img
            $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'img';
            $uploaded_slide_2->move($destinationPath, $filename);


            // hapus cover lama, jika ada
            if ($setting_slide->slide_2) {
            $old_slide_2 = $setting_slide->slide_2;
            $filepath = public_path() . DIRECTORY_SEPARATOR . 'img'
            . DIRECTORY_SEPARATOR . $setting_slide->slide_2;
            try {
            File::delete($filepath);
            } catch (FileNotFoundException $e) {
            // File sudah dihapus/tidak ada
            }
            }
            // mengisi field cover di book dengan filename yang baru dibuat
            $setting_slide->slide_2 = $filename;
            $setting_slide->save();
        }

        if ($request->hasFile('slide_3')) {
             $filename = null;
            // Mengambil file yang diupload
            $uploaded_slide_3 = $request->file('slide_3');
            // mengambil extension file
            $extension = $uploaded_slide_3->getClientOriginalExtension();
            // membuat nama file random berikut extension
            $filename = md5(microtime()) . '.' . $extension;
            // menyimpan cover ke folder public/img
            $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'img';
            $uploaded_slide_3->move($destinationPath, $filename);


            // hapus cover lama, jika ada
            if ($setting_slide->slide_3) {
            $old_slide_3 = $setting_slide->slide_3;
            $filepath = public_path() . DIRECTORY_SEPARATOR . 'img'
            . DIRECTORY_SEPARATOR . $setting_slide->slide_3;
            try {
            File::delete($filepath);
            } catch (FileNotFoundException $e) {
            // File sudah dihapus/tidak ada
            }
            }
            // mengisi field cover di book dengan filename yang baru dibuat
            $setting_slide->slide_3 = $filename;
            $setting_slide->save();
        }

          Session::flash("flash_notification", [
        "level"=>"success",
        "message"=>"Berhasil Mengubah Setting Slide Home"
        ]);


        return redirect()->route('setting_slide.index');
    }
 
}
