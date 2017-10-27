<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Role;
use App\User_otoritas;
use Auth;
use App\Angkatan;
use Session;
use App\ListKelompokMahasiswa;


class MasterUserController extends Controller
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
            $master_users = User::with('role');
            return Datatables::of($master_users)
             ->editColumn('name',function($user){
                return "<a href='".route('users.info',$user->id)."' class='btn-link'>".$user->name."</a>";
            })
            ->addColumn('action', function($master_user){
                    return view('datatable._action', [
                        'model'     => $master_user,
                        'form_url'  => route('master_users.destroy', $master_user->id),
                        'edit_url'  => route('master_users.edit', $master_user->id),
                        'confirm_message'   => 'Yakin Mau Menghapus User ' . $master_user->name . '?'
                        ]);
                })
            ->addColumn('konfirmasi', function($user_konfirmasi){
                    return view('master_users._action', [
                        'model'     => $user_konfirmasi,
                        'confirm_message'   => 'Apakah Anda Yakin Ingin Meng Konfirmasi User ' . $user_konfirmasi->name . '?',
                        'no_confirm_message'   => 'Apakah Anda Yakin Tidak Meng Konfirmasi User ' . $user_konfirmasi->name . '?',
                        'konfirmasi_url' => route('master_users.konfirmasi', $user_konfirmasi->id),
                        'no_konfirmasi_url' => route('master_users.no_konfirmasi', $user_konfirmasi->id),
                        ]);
                })//Konfirmasi User Apabila Bila Status User 1 Maka User sudah di konfirmasi oleh admin dan apabila status user 0 maka user belum di konfirmasi oleh admin

            ->addColumn('reset', function($reset){
                    return view('master_users._action_reset', [
                        'model'     => $reset,
                        'confirm_message'   => 'Apakah Anda Yakin Ingin Me Reset Password User ' . $reset->name . '?',
                        'reset_url' => route('master_users.reset', $reset->id),
                        ]);
                })//Reset Password apabila di klik tombol reset password maka password menjadi 123456 
            ->addColumn('role', function($user){
                $role = User_otoritas::with('role')->where('user_id',$user->id)->get();

                    return view('master_users._role', [ 
                        'model_role'     => $role,
                        'id_role' => $user->id,
                        ]); 
                }) 
            ->addColumn('angkatan',function($user){
                $role = User_otoritas::with('role')->where('user_id',$user->id)->where('role_id',3)->count();
                if ($role > 0 ) {
                    if ($user->id_angkatan != null) {
                        # code...
                    $angkatan = Angkatan::find($user->id_angkatan);
                    return $angkatan->nama_angkatan;
                    }
                    else{
                    return "";
                        
                    }
                }else{
                    return "";
                }

                })->make(true);
        }
        $html = $htmlBuilder
        ->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Nama'])
        ->addColumn(['data' => 'email', 'name' => 'email', 'title' => 'Username']) 
        ->addColumn(['data' => 'no_hp', 'name' => 'no_hp', 'title' => 'Nomor Hp', 'orderable' => false])
        ->addColumn(['data' => 'alamat', 'name' => 'alamat', 'title' => 'Alamat', 'orderable' => false])
        ->addColumn(['data' => 'role', 'name' => 'role', 'title' => 'Otoritas', 'orderable' => false, 'searchable'=>false])
        ->addColumn(['data' => 'angkatan', 'name' => 'angkatan', 'title' => 'Angkatan', 'orderable' => false, 'searchable'=>false])
        ->addColumn(['data' => 'reset', 'name' => 'reset', 'title' => 'Reset Password', 'orderable' => false, 'searchable'=>false])
        ->addColumn(['data' => 'konfirmasi', 'name' => 'konfirmasi', 'title' => 'Konfirmasi', 'orderable' => false, 'searchable'=>false])
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable'=>false]);

        $angkatan = Angkatan::all();
        $role = Role::all();

        return view('master_users.index',['angkatan' => $angkatan,'role' => $role])->with(compact('html'));
    }


    public function info_user($id){

        $user = User::find($id);
        $kelompok_mahasiswa = ListKelompokMahasiswa::where('id_mahasiswa',$id);

        return view('master_users.info',['user' => $user,'kelompok_mahasiswa' => $kelompok_mahasiswa]);
    }

 
    public function filter_konfirmasi(Request $request, Builder $htmlBuilder,$id)
    {
        //
        if ($request->ajax()) {
            # code...
            $master_users = User::with('role')->where('status',$id);
            return Datatables::of($master_users)
              ->editColumn('name',function($user){
                return "<a href='".route('users.info',$user->id)."' class='btn-link'>".$user->name."</a>";
            })
            ->addColumn('action', function($master_user){
                    return view('datatable._action', [
                        'model'     => $master_user,
                        'form_url'  => route('master_users.destroy', $master_user->id),
                        'edit_url'  => route('master_users.edit', $master_user->id),
                        'confirm_message'   => 'Yakin Mau Menghapus User ' . $master_user->name . '?'
                        ]);
                })
            ->addColumn('konfirmasi', function($user_konfirmasi){
                    return view('master_users._action', [
                        'model'     => $user_konfirmasi,
                        'confirm_message'   => 'Apakah Anda Yakin Ingin Meng Konfirmasi User ' . $user_konfirmasi->name . '?',
                        'no_confirm_message'   => 'Apakah Anda Yakin Tidak Meng Konfirmasi User ' . $user_konfirmasi->name . '?',
                        'konfirmasi_url' => route('master_users.konfirmasi', $user_konfirmasi->id),
                        'no_konfirmasi_url' => route('master_users.no_konfirmasi', $user_konfirmasi->id),
                        ]);
                })//Konfirmasi User Apabila Bila Status User 1 Maka User sudah di konfirmasi oleh admin dan apabila status user 0 maka user belum di konfirmasi oleh admin

            ->addColumn('reset', function($reset){
                    return view('master_users._action_reset', [
                        'model'     => $reset,
                        'confirm_message'   => 'Apakah Anda Yakin Ingin Me Reset Password User ' . $reset->name . '?',
                        'reset_url' => route('master_users.reset', $reset->id),
                        ]);
                })//Reset Password apabila di klik tombol reset password maka password menjadi 123456
            ->addColumn('role', function($user){
                $role = Role::where('id',$user->role->role_id)->first();
                return $role->display_name;
                })
            ->addColumn('angkatan',function($user){
                if ($user->role->role_id == 3) {
                    if ($user->id_angkatan != null) {
                        # code...
                    $angkatan = Angkatan::find($user->id_angkatan);
                    return $angkatan->nama_angkatan;
                    }
                    else{
                    return "";
                        
                    }
                }else{
                    return "";
                }

                })->make(true);
        }
        $html = $htmlBuilder
        ->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Namasasdadsad'])
        ->addColumn(['data' => 'email', 'name' => 'email', 'title' => 'Username']) 
        ->addColumn(['data' => 'no_hp', 'name' => 'no_hp', 'title' => 'Nomor Hp', 'orderable' => false])
        ->addColumn(['data' => 'alamat', 'name' => 'alamat', 'title' => 'Alamat', 'orderable' => false])
        ->addColumn(['data' => 'role', 'name' => 'role', 'title' => 'Otoritas', 'orderable' => false, 'searchable'=>false])
        ->addColumn(['data' => 'angkatan', 'name' => 'angkatan', 'title' => 'Angkatan', 'orderable' => false, 'searchable'=>false])
        ->addColumn(['data' => 'reset', 'name' => 'reset', 'title' => 'Reset Password', 'orderable' => false, 'searchable'=>false])
        ->addColumn(['data' => 'konfirmasi', 'name' => 'konfirmasi', 'title' => 'Konfirmasi', 'orderable' => false, 'searchable'=>false])
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable'=>false]);

        $angkatan = Angkatan::all();
        $role = Role::all();

        return view('master_users.index',['angkatan' => $angkatan,'role' => $role])->with(compact('html'));
    }

    public function filter_angkatan(Request $request, Builder $htmlBuilder,$id)
    {
        //
        if ($request->ajax()) {
            # code...
            $master_users = User::with('role')->where('id_angkatan',$id);
            return Datatables::of($master_users)
            ->editColumn('name',function($user){
                return "<a href='".route('users.info',$user->id)."' class='btn-link'>".$user->name."</a>";
            })
            ->addColumn('action', function($master_user){
                    return view('datatable._action', [
                        'model'     => $master_user,
                        'form_url'  => route('master_users.destroy', $master_user->id),
                        'edit_url'  => route('master_users.edit', $master_user->id),
                        'confirm_message'   => 'Yakin Mau Menghapus User ' . $master_user->name . '?'
                        ]);
                })
            ->addColumn('konfirmasi', function($user_konfirmasi){
                    return view('master_users._action', [
                        'model'     => $user_konfirmasi,
                        'confirm_message'   => 'Apakah Anda Yakin Ingin Meng Konfirmasi User ' . $user_konfirmasi->name . '?',
                        'no_confirm_message'   => 'Apakah Anda Yakin Tidak Meng Konfirmasi User ' . $user_konfirmasi->name . '?',
                        'konfirmasi_url' => route('master_users.konfirmasi', $user_konfirmasi->id),
                        'no_konfirmasi_url' => route('master_users.no_konfirmasi', $user_konfirmasi->id),
                        ]);
                })//Konfirmasi User Apabila Bila Status User 1 Maka User sudah di konfirmasi oleh admin dan apabila status user 0 maka user belum di konfirmasi oleh admin

            ->addColumn('reset', function($reset){
                    return view('master_users._action_reset', [
                        'model'     => $reset,
                        'confirm_message'   => 'Apakah Anda Yakin Ingin Me Reset Password User ' . $reset->name . '?',
                        'reset_url' => route('master_users.reset', $reset->id),
                        ]);
                })//Reset Password apabila di klik tombol reset password maka password menjadi 123456
            ->addColumn('role', function($user){
                $role = Role::where('id',$user->role->role_id)->first();
                return $role->display_name;
                })
            ->addColumn('angkatan',function($user){
                if ($user->role->role_id == 3) {
                    if ($user->id_angkatan != null) {
                        # code...
                    $angkatan = Angkatan::find($user->id_angkatan);
                    return $angkatan->nama_angkatan;
                    }
                    else{
                    return "";
                        
                    }
                }else{
                    return "";
                }

                })->make(true);
        }
        $html = $htmlBuilder
        ->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Nama'])
        ->addColumn(['data' => 'email', 'name' => 'email', 'title' => 'Username']) 
        ->addColumn(['data' => 'no_hp', 'name' => 'no_hp', 'title' => 'Nomor Hp', 'orderable' => false])
        ->addColumn(['data' => 'alamat', 'name' => 'alamat', 'title' => 'Alamat', 'orderable' => false])
        ->addColumn(['data' => 'role', 'name' => 'role', 'title' => 'Otoritas', 'orderable' => false, 'searchable'=>false])
        ->addColumn(['data' => 'angkatan', 'name' => 'angkatan', 'title' => 'Angkatan', 'orderable' => false, 'searchable'=>false])
        ->addColumn(['data' => 'reset', 'name' => 'reset', 'title' => 'Reset Password', 'orderable' => false, 'searchable'=>false])
        ->addColumn(['data' => 'konfirmasi', 'name' => 'konfirmasi', 'title' => 'Konfirmasi', 'orderable' => false, 'searchable'=>false])
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable'=>false]);

        $angkatan = Angkatan::all();
        $role = Role::all();

        return view('master_users.index',['angkatan' => $angkatan,'role' => $role])->with(compact('html'));
    }

    public function filter_otoritas(Request $request, Builder $htmlBuilder,$id)
    {
        //
        if ($request->ajax()) {
            # code... 

            $master_users = User::with('role')
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->where('role_id',$id);

            return Datatables::of($master_users)
            ->editColumn('name',function($user){
                return "<a href='".route('users.info',$user->id)."' class='btn-link'>".$user->name."</a>";
            })
            ->addColumn('action', function($master_user){
                    return view('datatable._action', [
                        'model'     => $master_user,
                        'form_url'  => route('master_users.destroy', $master_user->id),
                        'edit_url'  => route('master_users.edit', $master_user->id),
                        'confirm_message'   => 'Yakin Mau Menghapus User ' . $master_user->name . '?'
                        ]);
                })
            ->addColumn('konfirmasi', function($user_konfirmasi){
                    return view('master_users._action', [
                        'model'     => $user_konfirmasi,
                        'confirm_message'   => 'Apakah Anda Yakin Ingin Meng Konfirmasi User ' . $user_konfirmasi->name . '?',
                        'no_confirm_message'   => 'Apakah Anda Yakin Tidak Meng Konfirmasi User ' . $user_konfirmasi->name . '?',
                        'konfirmasi_url' => route('master_users.konfirmasi', $user_konfirmasi->id),
                        'no_konfirmasi_url' => route('master_users.no_konfirmasi', $user_konfirmasi->id),
                        ]);
                })//Konfirmasi User Apabila Bila Status User 1 Maka User sudah di konfirmasi oleh admin dan apabila status user 0 maka user belum di konfirmasi oleh admin

            ->addColumn('reset', function($reset){
                    return view('master_users._action_reset', [
                        'model'     => $reset,
                        'confirm_message'   => 'Apakah Anda Yakin Ingin Me Reset Password User ' . $reset->name . '?',
                        'reset_url' => route('master_users.reset', $reset->id),
                        ]);
                })//Reset Password apabila di klik tombol reset password maka password menjadi 123456
            ->addColumn('role', function($user){
                $role = Role::where('id',$user->role->role_id)->first();
                return $role->display_name;
                })
            ->addColumn('angkatan',function($user){
                if ($user->role->role_id == 3) {
                    if ($user->id_angkatan != null) {
                        # code...
                    $angkatan = Angkatan::find($user->id_angkatan);
                    return $angkatan->nama_angkatan;
                    }
                    else{
                    return "";
                        
                    }
                }else{
                    return "";
                }

                })->make(true);
        }
        $html = $htmlBuilder
        ->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Nama'])
        ->addColumn(['data' => 'email', 'name' => 'email', 'title' => 'Username']) 
        ->addColumn(['data' => 'no_hp', 'name' => 'no_hp', 'title' => 'Nomor Hp', 'orderable' => false])
        ->addColumn(['data' => 'alamat', 'name' => 'alamat', 'title' => 'Alamat', 'orderable' => false])
        ->addColumn(['data' => 'role', 'name' => 'role', 'title' => 'Otoritas', 'orderable' => false, 'searchable'=>false])
        ->addColumn(['data' => 'angkatan', 'name' => 'angkatan', 'title' => 'Angkatan', 'orderable' => false, 'searchable'=>false])
        ->addColumn(['data' => 'reset', 'name' => 'reset', 'title' => 'Reset Password', 'orderable' => false, 'searchable'=>false])
        ->addColumn(['data' => 'konfirmasi', 'name' => 'konfirmasi', 'title' => 'Konfirmasi', 'orderable' => false, 'searchable'=>false])
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable'=>false]);

        $angkatan = Angkatan::all();
        $role = Role::all();

        return view('master_users.index',['angkatan' => $angkatan,'role' => $role])->with(compact('html'));
    }

    public function konfirmasi($id){ 

            $master_users = User::find($id);   
            $master_users->status = 1;
            $master_users->save();  

        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"User Berhasil Di konfirmasi"
        ]);
 
        return redirect()->route('master_users.index');
    } 


    public function no_konfirmasi($id){ 

            $master_users = User::find($id);   
            $master_users->status = 0;
            $master_users->save();  

        Session::flash("flash_notification", [
            "level"=>"danger",
            "message"=>"User Tidak Di konfirmasi"
        ]);
 
        return redirect()->route('master_users.index');
    } 


    public function reset($id){ 

            $master_users = User::find($id);   
            $master_users->password = bcrypt('123456');
            $master_users->save();  

        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Password Berhasil Di Reset"
        ]);
 
        return redirect()->route('master_users.index');
    } 


    public function create()
    { 
        return view('master_users.create'); 
    }

    public function store(Request $request)
    {
        //
         $this->validate($request, [
            'name'   => 'required',
            'email'     => 'required|without_spaces|unique:users,email',
            'no_hp'    => 'required',
            'alamat'    => 'required',
            'role_id'    => 'required', 
            ]);


         $user_baru = User::create([ 
            'name' =>$request->name,
            'email'=>$request->email,
            'no_hp'=>$request->no_hp,
            'alamat'=>$request->alamat,
            'id_angkatan' => $request->id_angkatan, 
            'password' => bcrypt('123456')]);

            foreach ($request->role_id as $role) {  
                $user_baru->attachRole($role);
            }

        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Berhasil Menambah User $request->name"
            ]);

        return redirect()->route('master_users.index');
    }

    public function edit($id)
    {
        //
        $master_users = User::with('role')->find($id);
        $role = User_otoritas::with('role')->where('user_id',$id)->get();
            $data_role = '';
            foreach ($role as $roles) { 
              $data_role .= ( "'".$roles->role_id ."'," ); //untuk menampilkan data user yang sesuai ketika tambah
            }    

        return view('master_users.edit',['data_role'=>$data_role])->with(compact('master_users'));
    }
 
     public function update(Request $request, $id)
    {
        //
         $this->validate($request, [
            'name'   => 'required',
            'email'     => 'required|without_spaces|unique:users,email,' .$id,
            'no_hp'    => 'required',
            'alamat'    => 'required',
            'role_id'    => 'required', 
            ]);

        $user = User::where('id', $id)->update([ 
            'name' =>$request->name,
            'email'=>$request->email,
            'no_hp'=>$request->no_hp,
            'alamat'=>$request->alamat, 
            'id_angkatan' => $request->id_angkatan
            ]);

            User_otoritas::where('user_id', $id)->delete();  

            $user_baru = User::find($id);
            foreach ($request->role_id as $role) {  
                $user_baru->attachRole($role);
            }


        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Berhasil Mengubah User $request->name"
            ]);

        return redirect()->route('master_users.index');
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
        //
        
        $user_role = User::find($id);
        $otoritas = User_otoritas::where('user_id',$id)->first();
        $user_role->detachRole($otoritas->role_id);

        if (!User::destroy($id)) {
            return redirect()->back();
        }
        else{
            Session::flash("flash_notification", [
                "level"     => "danger",
                "message"   => "User Berhasil Di Hapus"
            ]);
        return redirect()->route('master_users.index');
        }
    }
}
