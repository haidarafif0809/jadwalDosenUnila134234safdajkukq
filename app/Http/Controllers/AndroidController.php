<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Master_ruangan;
use App\Penjadwalan;
use App\User;
use App\Jadwal_dosen;
use App\Presensi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


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
            // cek otoritas

                  if ($user_otoritas == 'dosen') {
                    // jika otoritas nya dosen maka login akan berhasil

                                $response["value"] = 1;// value = 1
                                $response["message"] = "Login Berhasil"; // login berhasil
                                return  json_encode($response);// data yang dikembalikan berupa json

                  }else{

                                $response["value"] = 2;// value = 2
                                $response["message"] = "Login Gagal, anda bukan dosen!!";// login gagal, kerena user bukan dosen
                                return  json_encode($response);// data yang dikembalikan berupa json
                  }


            }

            else {

                $response["value"] = 3;// value = 3
                $response["message"] = "Login Gagal";// login gagal
                return  json_encode($response);// data yang dikembalikan berupa json

            }


    }

//PROSES TAMBAH RUANGAN
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

//PROSES MEAMPILKAN RUANGAN
    public function list_ruangan(Request $request){        
      $ruangan =  Master_ruangan::all();
      $result = array();

        foreach ($ruangan as $row ) {       
          array_push($result, array('id'=>$row['id'], 'kode_ruangan'=>$row['kode_ruangan'], 'nama_ruangan'=>$row['nama_ruangan'], 'lokasi_ruangan' => $row['lokasi_ruangan'], 'latitude' => $row['latitude'], 'longitude' => $row['longitude'], 'batas_jarak_absen' => $row['batas_jarak_absen']));
        }

      echo json_encode(array("value"=>1,"result"=>$result));

    }

//PROSES UPDATE RUANGAN

    public function update_ruangan(Request $request) {

        Master_ruangan::where('id', $request->id) ->update([ 
            'kode_ruangan' =>$request->kode_ruangan,
            'nama_ruangan'=>$request->nama_ruangan,
            'lokasi_ruangan'=>$request->gedung,
            'longitude'=>$request->longitude,
            'latitude'=>$request->latitude,
            'batas_jarak_absen'=>$request->batas_jarak]);

        $response["value"] = 1;
        $response["message"] = "Ruangan Berhasil Diubah";
        
        return  json_encode($response);
    }


//PROSES HAPUS RUANGAN

    public function hapus_ruangan(Request $request)   {

        Master_ruangan::destroy($request->id);
        
        $response["value"] = 1;
        $response["message"] = "Ruangan Berhasil Dihapus";
        
        return  json_encode($response);
    }

// CARI RUANGAN
    public function cari_ruangan(Request $request){

        $search = $request->search;// REQUEST SEARCH

        $cari_ruangan =  Master_ruangan::select(['id','kode_ruangan','nama_ruangan','lokasi_ruangan','batas_jarak_absen','longitude','latitude'])->where(function($query) use ($search){// SEARCH
                              $query->orWhere('kode_ruangan','LIKE',$search.'%')// OR LIKE KODE RUANGAN
                                    ->orWhere('nama_ruangan','LIKE',$search.'%')// OR LIKE NAMA RUANGAN
                                    ->orWhere('lokasi_ruangan','LIKE',$search.'%')// OR LIKE LOKASI
                                    ->orWhere('batas_jarak_absen','LIKE',$search.'%');  //OR LIKE BATAS

                          })->orderBy('id', 'ASC')->get();
        $result = array();

        foreach ($cari_ruangan as $row ) {       
          array_push($result, array('id'=>$row['id'], 'kode_ruangan'=>$row['kode_ruangan'], 'nama_ruangan'=>$row['nama_ruangan'], 'lokasi_ruangan' => $row['lokasi_ruangan'], 'latitude' => $row['latitude'], 'longitude' => $row['longitude'], 'batas_jarak_absen' => $row['batas_jarak_absen']));
        }


      // DATA YANG DIKEMBALIKAN  BERUPA JSON
      return json_encode(array('value' => '1' , 'result'=>$result));


    }
// END CARI RUANGAN

// function tanggal terbalik
    function tanggal_terbalik($tanggal){
    
    $date= date_create($tanggal);
    $date_terbalik =  date_format($date,"d/m/Y");
    return $date_terbalik;
    }

// list jadwal dosen
    public function list_jadwal_dosen(Request $request){

        $dosen = $request->username;// DOSEN YANG LOGIN
        $id_dosen = User::select('id')->where('email',$dosen)->first();//  AMBIL ID DOSEN
        $value = 0;
        $result = array();// ARRAY RESULT

        $penjadwalans = Jadwal_dosen::select('jadwal_dosens.id_jadwal AS id_jadwal','jadwal_dosens.id_ruangan AS id_ruangan','jadwal_dosens.tanggal AS tanggal', 'jadwal_dosens.waktu_mulai AS waktu_mulai', 'jadwal_dosens.waktu_selesai AS waktu_selesai','master_mata_kuliahs.nama_mata_kuliah','master_ruangans.nama_ruangan AS ruangan')// DATA YANG DIAMBIL TANGGAL,WAKTU MULAI, WAKTU SELESAI, NAMA MATA KULIAH, DAN RUANGAN

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
                  array('tanggal' => $this->tanggal_terbalik($list_jadwal_dosen['tanggal']),
                          // TANGGAL DI FORMAT=> Y/M/D
                        'waktu' => $list_jadwal_dosen['waktu_mulai'] ." - " . $list_jadwal_dosen['waktu_selesai'],// WAKTU MULAI DAN WAKTU SELESAI DIJADIKAN SATU STRING
                        'mata_kuliah' => $list_jadwal_dosen['nama_mata_kuliah'],// MATA KULIAH
                        'nama_ruangan' => $list_jadwal_dosen['ruangan'], // NAMA RUANGAN
                        'id_jadwal' => $list_jadwal_dosen['id_jadwal'], // ID JADWAL
                        'id_ruangan' => $list_jadwal_dosen['id_ruangan'] // ID RUANGAN


                        )// ARRAY
                  );// ARRAY PUSH

      }// END FOREACH

     // DATA YANG DIKIRIM BERUPA JSON
      return json_encode(array('value' => $value , 'result'=>$result));

    }// end function list jadwal dosen

// search jadwal dosen
    public function search_jadwal_dosen(Request $request){

        $search = $request->search;// REQUEST SEARCH
        $dosen = $request->username;// DOSEN YANG LOGIN
        $id_dosen = User::select('id')->where('email',$dosen)->first();//  AMBIL ID DOSEN

        $result = array();// ARRAY RESULT

        $penjadwalans = Jadwal_dosen::select('jadwal_dosens.id_jadwal AS id_jadwal','jadwal_dosens.id_ruangan AS id_ruangan','jadwal_dosens.tanggal AS tanggal', 'jadwal_dosens.waktu_mulai AS waktu_mulai', 'jadwal_dosens.waktu_selesai AS waktu_selesai','master_mata_kuliahs.nama_mata_kuliah','master_ruangans.nama_ruangan AS ruangan')// DATA YANG DIAMBIL TANGGAL,WAKTU MULAI, WAKTU SELESAI, NAMA MATA KULIAH, DAN RUANGAN

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
                  array('tanggal' => $this->tanggal_terbalik($list_jadwal_dosen['tanggal']),
                          // TANGGAL DI FORMAT=> Y/M/D
                        'waktu' => $list_jadwal_dosen['waktu_mulai'] ." - " . $list_jadwal_dosen['waktu_selesai'],// WAKTU MULAI DAN WAKTU SELESAI DIJADIKAN SATU STRING
                        'mata_kuliah' => $list_jadwal_dosen['nama_mata_kuliah'],// MATA KULIAH
                        'nama_ruangan' => $list_jadwal_dosen['ruangan'], // NAMA RUANGAN
                        'id_jadwal' => $list_jadwal_dosen['id_jadwal'], // ID JADWAL
                        'id_ruangan' => $list_jadwal_dosen['id_ruangan'] // ID RUANGAN
                        )// ARRAY
                  );// ARRAY PUSH

      }// END FOREACH

      // DATA YANG DIKEMBALIKAN  BERUPA JSON
      return json_encode(array('value' => '1' , 'result'=>$result));


    }// end search jadawal dosen

// function batal jadwal dosen 
    public function batal_jadwal_dosen(Request $request)
    {
            $id_jadwal = $request->id_jadwal;// id jadwal

            $penjadwalan = Penjadwalan::where("id",$id_jadwal)->update(["status_jadwal" => 2]);
             // update Penjadwalan (status jadwal di set = 2 atau "Batal") where id_jadwal dosen = $id jadwal dosen

            $jadwal_dosen = Jadwal_dosen::where("id_jadwal",$id_jadwal)->update(["status_jadwal" => 2]);
            // update jadwal dosen (status jadwal di set = 2 atau "Batal") where id_jadwal dosen = $id jadwal dosen

            // jika query berhasil di eksekusi
            if ($penjadwalan ==  true AND $jadwal_dosen == true) {
              
            // DATA YANG DIKEMBALIKAN  BERUPA JSON
            return json_encode(array('value' => '1' , 'message'=>'Jadwal Berhasil Di Batalkan'));

            }else{

             // DATA YANG DIKEMBALIKAN  BERUPA JSON
            return json_encode(array('value' => '0' , 'message'=>'Jadwal Gagal Di Batalkan'));

            }



    }

// presendi dosen
    public function presensi_dosen(Request $request){

      $dosen = $request->username;// DOSEN YANG LOGIN
      $id_dosen = User::select('id')->where('email',$dosen)->first();//  AMBIL ID DOSEN
      $id_jadwal = $request->id_jadwal;// ID JADWAL
      $id_ruangan = $request->id_ruangan; // ID RUANGAN
      $longitude = "-";// LONGITUDE
      $latitude = "-";// LATITUDE
      $image = $request->image; // FOTO ABSEN

      // CEK APAKAH DOSEN INI SUDAH ABSEN BELUM UNTUK JADWAL INI
      $query_cek_presensi = Presensi::where('id_jadwal',$id_jadwal) // WHERE ID JADWAL
                          ->where('id_user',$id_dosen->id)// AND ID DOSEN
                          ->count();

          // JIKA 0, ARTINYA BELUM ABSEN
          if ($query_cek_presensi == 0) {

              // INSERT KE TABLE PRESENSI
                $presensi = Presensi::create([
                'id_user' => $id_dosen->id,// ID USER DOSEN
                'id_jadwal' => $id_jadwal,// ID JADWAL
                'id_ruangan' => $id_ruangan,// ID JADWAL
                'longitude' => $longitude,// LONGITUDE
                'latitude' => $latitude// LATITUDE
                ]);

                // MEMBUAT NAMA FILE DENGAN EXTENSI PNG 
                $filename = 'image' . DIRECTORY_SEPARATOR . str_random(40) . '.png';

                // UPLOAD FOTO
                file_put_contents($filename,base64_decode($image));
                 
                // INSERT FOTO KE TABLE PRSENSI   
                $presensi->foto = $filename;     
                $presensi->save();  

                // CEK ADA BERAPA DOSEN UNTUK JADWAL INI 
                $count_jadwal_dosen = Jadwal_dosen::where('id_jadwal',$id_jadwal)->count();
                
                // CEK ADA BERAPA DOSEN YANG SUDAH HADIR UNTUK JADWAL INI       
                $count_presensi = Presensi::where('id_jadwal',$id_jadwal)->count(); 

                  // JIKA SAMA
                  if ($count_jadwal_dosen == $count_presensi) {                  

                    // MAKA JADWAL AKAN DIUPDATE STATUSNYA MENJADI TERLAKSANA

                    $penjadwalan_terlaksana = Penjadwalan::where("id",$id_jadwal)->update(["status_jadwal" => 1]);
                     // update Penjadwalan (status jadwal di set = 1 atau "TERLAKSANA") where id_jadwal dosen = $id jadwal dosen

                    $jadwal_dosen_terlaksana = Jadwal_dosen::where("id_jadwal",$id_jadwal)->update(["status_jadwal" => 1]);
                    // update jadwal dosen (status jadwal di set = 1 atau "TERLAKSANA") where id_jadwal dosen = $id jadwal dosen

                  }

                $response["value"] = 1;// RESPONSE VALUE 1
                $response["message"] = "Berhasil Absen";// RESPONSE BERHASIL ABSEN
                // DATA DIKEMBALIKAN DALAM BENTUK JSON
                return  json_encode($response);

            
          }else{// JIKA TIDAK NOL, MAKA DOSEN SUDAH ABSEN


                $response["value"] = 0;// RESPONSE VALUE 0
                $response["message"] = "Gagal Absen";// RESPONSE Gagal ABSEN
                // DATA DIKEMBALIKAN DALAM BENTUK JSON
                return  json_encode($response);

          }// END          
    
    }// PRESENSI

}
