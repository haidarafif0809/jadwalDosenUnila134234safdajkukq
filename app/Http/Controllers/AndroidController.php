<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Master_ruangan;
use App\Penjadwalan;
use App\User;
use App\Jadwal_dosen;
use Illuminate\Support\Facades\DB;

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

    function tanggal_terbalik($tanggal){
    
    $date= date_create($tanggal);
    $date_terbalik =  date_format($date,"d/m/Y");
    return $date_terbalik;
    }

    public function list_jadwal_dosen(Request $request){

        $dosen = $request->username;// DOSEN YANG LOGIN
        $id_dosen = User::select('id')->where('email',$dosen)->first();//  AMBIL ID DOSEN
        $value = 0;
        $result = array();// ARRAY RESULT

        $penjadwalans = Jadwal_dosen::select('jadwal_dosens.id_jadwal AS id_jadwal','jadwal_dosens.tanggal AS tanggal', 'jadwal_dosens.waktu_mulai AS waktu_mulai', 'jadwal_dosens.waktu_selesai AS waktu_selesai','master_mata_kuliahs.nama_mata_kuliah','master_ruangans.nama_ruangan AS ruangan')// DATA YANG DIAMBIL TANGGAL,WAKTU MULAI, WAKTU SELESAI, NAMA MATA KULIAH, DAN RUANGAN

                        ->leftJoin('master_mata_kuliahs','jadwal_dosens.id_mata_kuliah','=','master_mata_kuliahs.id')
                        //LEFT JOIN KE TABLE MATA KULIAH
                        ->leftJoin('master_ruangans','jadwal_dosens.id_ruangan','=','master_ruangans.id')
                        // LEFT JOIN MASTER RUANGAN
                        ->where('jadwal_dosens.id_dosen',$id_dosen->id)
                        //WHERE ID DOSEN = ID DOSEN LOGIN
                        ->where(DB::raw('CONCAT(jadwal_dosens.tanggal, " ", jadwal_dosens.waktu_mulai)'),'>=',date("Y-m-d H:i:s"))
                        // JADWAL YANG DIAMBIL ADALAH JADWAL YANG AKAN DATANG, JADWAL YANG SUDAH LEWAT TIDAK AKAN TAMPIL
                        ->where('jadwal_dosens.status_jadwal',0)
                        // YANG DITAMPILKAN HANYA JADWAL YANG BELUM TERLAKSANA
                        ->orderBy(DB::raw('CONCAT(jadwal_dosens.tanggal, " ", jadwal_dosens.waktu_mulai)', 'ASC'))
                        // DITAMPILKAN BERDASARKAN WAKTU TERDEKAT
                        ->groupBy('jadwal_dosens.id_jadwal')// GROUP BY ID JADWAL
                        ->get();


      foreach ($penjadwalans as $list_jadwal_dosen) {// FOREACH
        $value = $value + 1;
        //ARRAY PUSH
        array_push($result, 
                  array('tanggal' => AndroidController::tanggal_terbalik($list_jadwal_dosen['tanggal']),
                          // TANGGAL DI FORMAT=> Y/M/D
                        'waktu' => $list_jadwal_dosen['waktu_mulai'] ." - " . $list_jadwal_dosen['waktu_selesai'],// WAKTU MULAI DAN WAKTU SELESAI DIJADIKAN SATU STRING
                        'mata_kuliah' => $list_jadwal_dosen['nama_mata_kuliah'],// MATA KULIAH
                        'nama_ruangan' => $list_jadwal_dosen['ruangan'], // NAMA RUANGAN
                        'id_jadwal' => $list_jadwal_dosen['id_jadwal'] // ID JADWAL


                        )// ARRAY
                  );// ARRAY PUSH

      }// END FOREACH

     // DATA YANG DIKIRIM BERUPA JSON
      return json_encode(array('value' => $value , 'result'=>$result));

    }


    public function search_jadwal_dosen(Request $request){

        $search = $request->search;// REQUEST SEARCH
        $dosen = $request->username;// DOSEN YANG LOGIN
        $id_dosen = User::select('id')->where('email',$dosen)->first();//  AMBIL ID DOSEN

        $result = array();// ARRAY RESULT

        $penjadwalans = Jadwal_dosen::select('jadwal_dosens.id_jadwal AS id_jadwal','jadwal_dosens.tanggal AS tanggal', 'jadwal_dosens.waktu_mulai AS waktu_mulai', 'jadwal_dosens.waktu_selesai AS waktu_selesai','master_mata_kuliahs.nama_mata_kuliah','master_ruangans.nama_ruangan AS ruangan')// DATA YANG DIAMBIL TANGGAL,WAKTU MULAI, WAKTU SELESAI, NAMA MATA KULIAH, DAN RUANGAN

                        ->leftJoin('master_mata_kuliahs','jadwal_dosens.id_mata_kuliah','=','master_mata_kuliahs.id')
                        //LEFT JOIN KE TABLE MATA KULIAH
                        ->leftJoin('master_ruangans','jadwal_dosens.id_ruangan','=','master_ruangans.id')
                        // LEFT JOIN MASTER RUANGAN
                        ->where('jadwal_dosens.id_dosen',$id_dosen->id)
                        //WHERE ID DOSEN = ID DOSEN LOGIN
                        ->where(DB::raw('CONCAT(jadwal_dosens.tanggal, " ", jadwal_dosens.waktu_mulai)'),'>=',date("Y-m-d H:i:s"))
                        // JADWAL YANG DIAMBIL ADALAH JADWAL YANG AKAN DATANG, JADWAL YANG SUDAH LEWAT TIDAK AKAN TAMPIL
                        ->where('jadwal_dosens.status_jadwal',0)                        
                        // YANG DITAMPILKAN HANYA JADWAL YANG BELUM TERLAKSANA  
                        ->where(function($query) use ($search){// search
                            $query->orWhere('jadwal_dosens.tanggal','LIKE',$search.'%')// OR LIKE TANGGAL
                                  ->orWhere(DB::raw('DATE_FORMAT(jadwal_dosens.tanggal, "%d/%m/%Y")'),'LIKE',$search.'%')// OR LIKE FORMAT TANGGAL d/m/y
                                  ->orWhere(DB::raw('DATE_FORMAT(jadwal_dosens.tanggal, "%d-%m-%Y")'),'LIKE',$search.'%')// OR LIKE FORMAT TANGGAL d-m-y
                                  ->orWhere('jadwal_dosens.waktu_mulai','LIKE',$search.'%')// OR LIKE WAKTU MULAI
                                  ->orWhere('master_mata_kuliahs.nama_mata_kuliah','LIKE',$search.'%')// OR LIKE NAMA MATA KULIAH
                                  ->orWhere('master_ruangans.nama_ruangan','LIKE',$search.'%');  //OR LIKE NAMA RUANGAN
                        })    // search  
                        ->orderBy(DB::raw('CONCAT(jadwal_dosens.tanggal, " ", jadwal_dosens.waktu_mulai)', 'ASC'))
                        // DITAMPILKAN BERDASARKAN WAKTU TERDEKAT
                        ->groupBy('jadwal_dosens.id_jadwal')// GROUP BY ID JADWAL
                        ->get();


      foreach ($penjadwalans as $list_jadwal_dosen) {// FOREACH

        //ARRAY PUSH
        array_push($result, 
                  array('tanggal' => AndroidController::tanggal_terbalik($list_jadwal_dosen['tanggal']),
                          // TANGGAL DI FORMAT=> Y/M/D
                        'waktu' => $list_jadwal_dosen['waktu_mulai'] ." - " . $list_jadwal_dosen['waktu_selesai'],// WAKTU MULAI DAN WAKTU SELESAI DIJADIKAN SATU STRING
                        'mata_kuliah' => $list_jadwal_dosen['nama_mata_kuliah'],// MATA KULIAH
                        'nama_ruangan' => $list_jadwal_dosen['ruangan'], // NAMA RUANGAN
                        'id_jadwal' => $list_jadwal_dosen['id_jadwal'] // ID JADWAL
                        )// ARRAY
                  );// ARRAY PUSH

      }// END FOREACH

      // DATA YANG DIKIRIM BERUPA JSON
      return json_encode(array('value' => '1' , 'result'=>$result));


    }

    public function batal_jadwal_dosen(Request $request)
    {
            $id_jadwal = $request->id_jadwal;
            $penjadwalan = Penjadwalan::find($id_jadwal);   
            $penjadwalan->status_jadwal = 2;
            $penjadwalan->save();  

            $jadwal_dosen = Jadwal_dosen::where("id_jadwal",$id_jadwal)->update(["status_jadwal" => 2]);

                  // DATA YANG DIKIRIM BERUPA JSON
            return json_encode(array('value' => '1' , 'message'=>'Jadwal Berhasil Di Batalkan'));

    }


    
}
