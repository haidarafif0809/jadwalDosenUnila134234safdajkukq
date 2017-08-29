<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Master_ruangan;

class AndroidController extends Controller
{
    //

        public function authenticate(Request $request)
    {

        if (Auth::attempt(['email' => $request->username, 'password' => $request->password])) {
            // Authentication passed...
            $response["value"] = 1;
       		$response["message"] = "Login Berhasil";
       		return  json_encode($response);
        }
        else {
        	$response["value"] = 2;
       		$response["message"] = "Login Gagal";
       		return  json_encode($response);
        }
    }

    public function login_dosen_android(Request $request){


        if (Auth::attempt(['email' => $request->username, 'password' => $request->password])) {
            // Authentication passed...

            $user_otoritas = Auth::user()->roles->first()->name;

                  if ($user_otoritas == 'dosen') {

                                $response["value"] = 1;
                                $response["message"] = "Login Berhasil";
                                return  json_encode($response);

                  }else{

                                $response["value"] = 2;
                                $response["message"] = "Login Gagal, anda bukan dosen!!";
                                return  json_encode($response);
                  }


            }

            else {

                $response["value"] = 3;
                $response["message"] = "Login Gagal";
                return  json_encode($response);

            }


    }

    public function tambah_ruangan (Request $request){

        $this->validate($request, [
            'kode_ruangan'   => 'required|unique:master_ruangans,kode_ruangan,'
            ]);

         $master_ruangans = Master_ruangan::create([ 
            'kode_ruangan' =>$request->kode_ruangan,
            'nama_ruangan'=>$request->nama_ruangan,
            'lokasi_ruangan'=>$request->gedung,
            'longitude'=>$request->longitude,
            'latitude'=>$request->latitude,
            'batas_jarak_absen'=>$request->batas_jarak]);

          $response["value"] = 1;
          $response["message"] = "Ruangan Berhasil Ditambah";
          return  json_encode($response);
    }
    public function list_ruangan(Request $request){
        
       $ruangan =  Master_ruangan::all();
        $result = array();
      foreach ($ruangan as $row ) {
       
        array_push($result, array('kode_ruangan'=>$row['kode_ruangan'], 'nama_ruangan'=>$row['nama_ruangan'], 'nama' => $row['nama'],'foto_masuk' => $row['gambar']));
      }
        echo json_encode(array("value"=>1,"result"=>$result));
    }


    
}
