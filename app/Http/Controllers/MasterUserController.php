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
                    $angkatan = Angkatan::find($user->id_angkatan);
                    return $angkatan->nama_angkatan;
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

        return view('master_users.index')->with(compact('html'));
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
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $master_users = User::with('role')->find($id);

        return view('master_users.edit')->with(compact('master_users'));
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
            'name'   => 'required',
            'email'     => 'required|unique:users,email,' .$id,
            'no_hp'    => 'required',
            'alamat'    => 'required',
            'role_id'    => 'required',
            'role_lama'    => 'required',
            ]);

        $user = User::where('id', $id) ->update([ 
            'name' =>$request->name,
            'email'=>$request->email,
            'no_hp'=>$request->no_hp,
            'alamat'=>$request->alamat,
            'id_angkatan' => $request->id_angkatan
            ]);

        $role_lama = Role::where('id',$request->role_lama)->first();
        $role_baru = Role::where('id',$request->role_id)->first();
        $user_baru = User::find($id);

        $user_baru->detachRole($role_lama->id);

        $user_baru->attachRole($role_baru->id);

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
