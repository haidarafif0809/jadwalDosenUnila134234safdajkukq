<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\KelompokMahasiswa;
use App\ListKelompokMahasiswa;
use App\Penjadwalan;
use App\Angkatan;
use Session;

class KelompokMahasiswaController extends Controller
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
            $kelompok_mahasiswa = KelompokMahasiswa::with('angkatan');
            return Datatables::of($kelompok_mahasiswa)->addColumn('action', function($kelompok_mahasiswa){
                return view('datatable._action', [
                'model'             => $kelompok_mahasiswa,
                'form_url'          => route('kelompok_mahasiswa.destroy', $kelompok_mahasiswa->id),
                'edit_url'          => route('kelompok_mahasiswa.edit', $kelompok_mahasiswa->id),
                'confirm_message'   => 'Apakah Anda Yakin Ingin Menghapus Block ' .$kelompok_mahasiswa->nama_kelompok_mahasiswa. '?'
                ]);
            })->addColumn('angkatan', function($kelompok_mahasiswa){

                if ($kelompok_mahasiswa->id_angkatan == NULL) {
                    return "Belum di kaitkan";
                }
                else {
                    $angkatan = Angkatan::find($kelompok_mahasiswa->id_angkatan);

                    return $angkatan->nama_angkatan; 
                }
               
            })->addColumn('mahasiswa', function($kelompok_mahasiswa){ 
                return '<a class="btn btn-default" href="'.route('kelompok_mahasiswa.mahasiswa',$kelompok_mahasiswa->id).'">Lihat Mahasiswa</a>';
            })->make(true);

             }
        $html = $htmlBuilder
        ->addColumn(['data' => 'nama_kelompok_mahasiswa', 'name' => 'nama_kelompok_mahasiswa', 'title' => 'Nama Kelompok Mahasiswa'])
        ->addColumn(['data' => 'angkatan', 'name' => 'angkatan', 'title' => 'Angkatan','orderable' => false, 'searchable' => false])
        ->addColumn(['data' => 'jenis_kelompok', 'name' => 'jenis_kelompok', 'title' => 'Jenis Kelompok','orderable' => false, 'searchable' => false])
        ->addColumn(['data' => 'mahasiswa', 'name' => 'mahasiswa', 'title' => 'Mahasiswa','orderable' => false, 'searchable' => false])
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable' => false]);

        return view('kelompok_mahasiswa.index')->with(compact('html'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
 
        return view('kelompok_mahasiswa.create'); 
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
            'nama_kelompok_mahasiswa'   => 'required|unique:kelompok_mahasiswas,nama_kelompok_mahasiswa,',
            'jenis_kelompok' => 'required'
            ]);

         $kelompok_mahasiswa = KelompokMahasiswa::create([ 
            'nama_kelompok_mahasiswa' =>$request->nama_kelompok_mahasiswa,
            'id_angkatan'=>$request->id_angkatan,
            'jenis_kelompok'=>$request->jenis_kelompok
            ]);

        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Berhasil Menambah  $kelompok_mahasiswa->nama_kelompok_mahasiswa"
            ]);
        return redirect()->route('kelompok_mahasiswa.index');
     
    }

    // tombol lihat mahasiswa untuk menambahkan list mahasiswa di kelompok  mahasiswa
    public function createMahasiswa(Request $request, Builder $htmlBuilder,$id)
    {

        $mahasiswa = DB::table('users')
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id',3)
            ->pluck('name','id');
        $kelompok = KelompokMahasiswa::find($id);

        //datatable 
        if ($request->ajax()) {
            $mahasiswa_kelompok_mahasiswa = ListKelompokMahasiswa::with('mahasiswa')->where('id_kelompok_mahasiswa',$id);
            return Datatables::of($mahasiswa_kelompok_mahasiswa)->addColumn('action', function($mahasiswa_kelompok_mahasiswa){
                return view('datatable._hapus', [
                    'model'             => $mahasiswa_kelompok_mahasiswa,
                    'form_url'          => route('list_kelompok_mahasiswa.destroy', $mahasiswa_kelompok_mahasiswa->id),
                  
                    'confirm_message'   => 'Apakah Anda Yakin Ingin Menghapus Mahasiswa ' .$mahasiswa_kelompok_mahasiswa->mahasiswa->name. '?'
                ]);
            })->make(true);
        }

        $html = $htmlBuilder
        ->addColumn(['data' => 'mahasiswa.name', 'name' => 'mahasiswa.name', 'title' => 'Mahasiswa', 'orderable' => false])
           ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable' => false]);



        return view('kelompok_mahasiswa.create_mahasiswa',['id' =>$id,'mahasiswa' => $mahasiswa,'kelompok' => $kelompok])->with(compact('html'));
    }

    public function hapus_list_kelompok_mahasiswa($id){
        ListKelompokMahasiswa::destroy($id);

          Session::flash("flash_notification", [
                "level"     => "danger",
                "message"   => "Mahasiswa Berhasil Di Hapus"
            ]);
        return redirect()->back();

    }

     public function proses_kait_list_kelompok_mahasiswa(Request $request,$id)
    {

         $this->validate($request, [
            'mahasiswa'    => 'required|unique:list_kelompok_mahasiswas,id_kelompok_mahasiswa,NULL,id,id_kelompok_mahasiswa,'.$id
            ]);   

         ListKelompokMahasiswa::create([
            'id_mahasiswa'=> $request->mahasiswa,
            'id_kelompok_mahasiswa' => $id
        ]);

         Session::flash("flash_notification", [
            "level"     => "success",
            "message"   => "Berhasil Mengaitkan Mahasiswa ke Kelompok Mahasiswa"
        ]);

         return redirect()->back();
    
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
        $kelompok = KelompokMahasiswa::find($id);
        $users = DB::table('users')
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id',5)
            ->pluck('name','id');

        return view('kelompok_mahasiswa.edit',['users' => $users])->with(compact('kelompok'));
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
            'nama_kelompok_mahasiswa'   => 'required|unique:kelompok_mahasiswas,nama_kelompok_mahasiswa,'.$id,
            'jenis_kelompok' => 'required'
            ]);

        KelompokMahasiswa::where('id', $id)->update([            
            'nama_kelompok_mahasiswa' =>$request->nama_kelompok_mahasiswa,
            'id_angkatan'=>$request->id_angkatan,
            'jenis_kelompok'=>$request->jenis_kelompok
        ]);

        Session::flash("flash_notification", [
            "level"     => "success",
            "message"   => "Berhasil Mengubah $request->nama_kelompok_mahasiswa"
        ]);

        return redirect()->route('kelompok_mahasiswa.index');
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
        $penjadwalan = Penjadwalan::where('id_kelompok',$id); 
 
        if ($penjadwalan->count() > 0) {
        // menyiapkan pesan error
        $html = 'Kelompok Mahasiswa tidak bisa dihapus karena masih memiliki Penjadwalan'; 
        
        Session::flash("flash_notification", [
          "level"=>"danger",
          "message"=>$html
        ]); 

        return redirect()->route('kelompok_mahasiswa.index');      
        }
        else{
        KelompokMahasiswa::destroy($id);
        ListKelompokMahasiswa::where('id_kelompok_mahasiswa', $id)->delete();

            Session::flash("flash_notification", [
                "level"     => "danger",
                "message"   => "Master Data Kelompok Mahasiswa Berhasil Di Hapus"
            ]);
        return redirect()->route('kelompok_mahasiswa.index');
        }
    }
}
