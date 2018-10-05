<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Master_ruangan;
use App\Penjadwalan;
use App\User;
use App\Jadwal_dosen;
use App\Presensi;
use App\PresensiMahasiswa;
use App\Master_block;
use App\Materi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\ListKelompokMahasiswa;
use File;


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

          //status nya sudah di konfirmasi 
      if ( Auth::user()->status == 1) {




        $user_otoritas = Auth::user()->roles->first()->name;
            // cek otoritas

        if ($user_otoritas == 'dosen') {
                    // jika otoritas nya dosen maka login akan berhasil

                                $response["value"] = 1;// value = 1
                                $response["message"] = "Login Berhasil"; // login berhasil
                                return  json_encode($response);// data yang dikembalikan berupa json
                  //login gagal karena bukan dosen
                              }else{

                                $response["value"] = 2;// value = 2
                                $response["message"] = "Login Gagal, anda bukan dosen!!";// login gagal, kerena user bukan dosen
                                return  json_encode($response);// data yang dikembalikan berupa json
                              }


                            }

                //status nya sudah belum di konfirmasi 
                            else {

                  $response["value"] = 3;// value = 3
                  $response["message"] = "Anda Tidak Bisa Login Di Karenakan Belum Di Konfirmasi Oleh Admin";// login gagal
                  return  json_encode($response);

                }

              }
             //password nya salah
              else {

                $response["value"] = 3;// value = 3
                $response["message"] = "username atau password salah";// login gagal
                return  json_encode($response);

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


      function tanggal_mysql($tanggal2){

       $date= date_create($tanggal2);
       $date_format = date_format($date,"Y-m-d");
       return $date_format;
     }

// list jadwal dosen
     public function list_jadwal_dosen(Request $request){

        $dosen = $request->username;// DOSEN YANG LOGIN
        $query_dosen = User::select('id')->where('email',$dosen)->first();//  AMBIL ID DOSEN
        $id_dosen = $query_dosen->id;
        $value = 0;
        $result = array();// ARRAY RESULT

        // QUERY LENGKAP NYA ADA DI MODEL JADWAL DOSEN , DISINI KITA MENGGUNAKAN SCOPE(ListJadwalDosen)  
        $penjadwalans = Jadwal_dosen::ListJadwalDosen($id_dosen)->get();


      foreach ($penjadwalans as $list_jadwal_dosen) {// FOREACH
        $value = $value + 1;

          // jika tipe jadwal nya kosong atau null
        if (($list_jadwal_dosen['id_mata_kuliah'] == "" OR $list_jadwal_dosen['id_mata_kuliah'] == NULL OR $list_jadwal_dosen['id_mata_kuliah'] == 0) AND ($list_jadwal_dosen['tipe_jadwal'] == "CSL" OR $list_jadwal_dosen['tipe_jadwal'] == 'TUTORIAL' )) {
              // maka tipe jadwal = -

          $materi = Materi::select('nama_materi')->where('id',$list_jadwal_dosen['id_materi'])->first();
          $nama_mata_kuliah = $materi->nama_materi;       
          $jadwal = Jadwal_dosen::with('ruangan')->where('id_jadwal',$list_jadwal_dosen['id_jadwal'])->first();  
          $nama_ruangan = $jadwal->ruangan->nama_ruangan;  
          $id_ruangan = $jadwal->ruangan->id;   
          $latitude = $jadwal->ruangan->latitude;   
          $longitude = $jadwal->ruangan->longitude;  
          $batas_jarak_absen = $jadwal->ruangan->batas_jarak_absen;          

            }else{ // jika tidak
              $nama_mata_kuliah = $list_jadwal_dosen['nama_mata_kuliah'];              
              $nama_ruangan = $list_jadwal_dosen['ruangan']; 
              $id_ruangan = $list_jadwal_dosen['id_ruangan'];   
              $latitude = $list_jadwal_dosen['latitude'];   
              $longitude = $list_jadwal_dosen['longitude'];  
              $batas_jarak_absen = $list_jadwal_dosen['batas_jarak_absen'];  
            }

        //ARRAY PUSH
            array_push($result, 
              array('tanggal' => $this->tanggal_terbalik($list_jadwal_dosen['tanggal']),
                          // TANGGAL DI FORMAT=> Y/M/D
                        'waktu' => $list_jadwal_dosen['waktu_mulai'] ." - " . $list_jadwal_dosen['waktu_selesai'],// WAKTU MULAI DAN WAKTU SELESAI DIJADIKAN SATU STRING
                        'mata_kuliah' => $nama_mata_kuliah,// MATA KULIAH
                        'nama_ruangan' => $nama_ruangan, // NAMA RUANGAN
                        'id_jadwal' => $list_jadwal_dosen['id_jadwal'], // ID JADWAL
                        'id_ruangan' => $id_ruangan, // ID RUANGAN
                      'latitude' => $latitude, // LATITUDE
                      'longitude' => $longitude, // LONGITUDE
                      'batas_jarak_absen' => $batas_jarak_absen, // BATAS JARAK ABSEN
                        'tipe_jadwal' => $list_jadwal_dosen['tipe_jadwal'] // TIPE JADWAL


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
        $query_dosen = User::select('id')->where('email',$dosen)->first();//  AMBIL ID DOSEN
        $id_dosen = $query_dosen->id;

        $result = array();// ARRAY RESULT
        
        // QUERY LENGKAP NYA ADA DI MODEL JADWAL DOSEN , DISINI KITA MENGGUNAKAN SCOPE(ListJadwalDosen)  
        $penjadwalans = Jadwal_dosen::SearchJadwalDosen($id_dosen,$search)
        ->get();


      foreach ($penjadwalans as $list_jadwal_dosen) {// FOREACH


          // jika tipe jadwal nya kosong atau null
        if (($list_jadwal_dosen['id_mata_kuliah'] == "" OR $list_jadwal_dosen['id_mata_kuliah'] == NULL OR $list_jadwal_dosen['id_mata_kuliah'] == 0) AND ($list_jadwal_dosen['tipe_jadwal'] == "CSL" OR $list_jadwal_dosen['tipe_jadwal'] == 'TUTORIAL' )) {
              // maka tipe jadwal = -
          $materi = Materi::select('nama_materi')->where('id',$list_jadwal_dosen['id_materi'])->first();
          $nama_mata_kuliah = $materi->nama_materi;     
          $jadwal = Jadwal_dosen::with('ruangan')->where('id_jadwal',$list_jadwal_dosen['id_jadwal'])->first();  
          $nama_ruangan = $jadwal->ruangan->nama_ruangan;  
          $id_ruangan = $jadwal->ruangan->id;   
          $latitude = $jadwal->ruangan->latitude;   
          $longitude = $jadwal->ruangan->longitude;  
          $batas_jarak_absen = $jadwal->ruangan->batas_jarak_absen;        

            }else{ // jika tidak
              $nama_mata_kuliah = $list_jadwal_dosen['nama_mata_kuliah'];
              $nama_ruangan = $list_jadwal_dosen['ruangan'];
              $id_ruangan = $list_jadwal_dosen['id_ruangan'];   
              $latitude = $list_jadwal_dosen['latitude'];   
              $longitude = $list_jadwal_dosen['longitude'];  
              $batas_jarak_absen = $list_jadwal_dosen['batas_jarak_absen'];  
            }

        //ARRAY PUSH
            array_push($result, 
              array('tanggal' => $this->tanggal_terbalik($list_jadwal_dosen['tanggal']),
                          // TANGGAL DI FORMAT=> Y/M/D
                        'waktu' => $list_jadwal_dosen['waktu_mulai'] ." - " . $list_jadwal_dosen['waktu_selesai'],// WAKTU MULAI DAN WAKTU SELESAI DIJADIKAN SATU STRING
                        'mata_kuliah' => $nama_mata_kuliah,// MATA KULIAH
                        'nama_ruangan' => $nama_ruangan, // NAMA RUANGAN
                        'id_jadwal' => $list_jadwal_dosen['id_jadwal'], // ID JADWAL
                        'id_ruangan' => $id_ruangan, // ID RUANGAN
                        'latitude' => $latitude, // LATITUDE
                        'longitude' => $longitude, // LONGITUDE
                        'batas_jarak_absen' => $batas_jarak_absen, // BATAS JARAK ABSEN
                        'tipe_jadwal' => $list_jadwal_dosen['tipe_jadwal'] // TIPE JADWAL 
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
      $longitude = $request->longitude_sekarang;// LONGITUDE
      $latitude = $request->latitude_sekarang;// LATITUDE
      $image = $request->image; // FOTO ABSEN
      $jarak_ke_lokasi_absen = $request->jarak_ke_lokasi_absen; // JARK KE LOKASI ABSEN
      $waktu = date("Y-m-d H:i:s");// WAKTU SEKARANG
      $tanggal1 = $request->tanggal; // TANGGAL JADWAL
      $tanggal = Carbon::createFromFormat('d/m/Y', $tanggal1)->format('Y-m-d');// UBAH FORMAT TANGGAL D/M/Y MENJADI Y-M-D
      $waktu_jadwal = $request->waktu_jadwal;// WAKTU JADWAL (08:00 - 09:00)

      // EXPLODE WAKTU JADWAL
      $waktu_jadwal_dosen = explode(" - ", $waktu_jadwal); 
      $waktu_mulai = $waktu_jadwal_dosen[0]; // EXPLODE 0 ADALAH WAKTU MULAI
      $waktu_selesai = $waktu_jadwal_dosen[1]; // EXPLODE 1 ADALAH WAKTU SELESAI


      $waktu_jadwal_mulai = $tanggal ." ". $waktu_mulai;    // TANGGAL JADAWL DIJADIKAN SATU STRING DENGAN WAKTU MULAI  
      $waktu_jadwal_selesai = $tanggal ." ". $waktu_selesai;  // TANGGAL JADAWL DIJADIKAN SATU STRING DENGAN WAKTU SELESAI

      // JIKA WAKTU JADWAL SUDAH MULAI
      if ($waktu >= $waktu_jadwal_mulai AND $waktu <= $waktu_jadwal_selesai) {// JIKA WAKTU SAAT INI BERADA DIANTARA WAKTU MULAI DAN WAKTU SELESAI

      // CEK APAKAH DOSEN INI SUDAH ABSEN BELUM UNTUK JADWAL INI
      $query_cek_presensi = Presensi::where('id_jadwal',$id_jadwal) // WHERE ID JADWAL
                          ->where('id_user',$id_dosen->id)// AND ID DOSEN
                          ->count();

                          // ambil id block
                          $query_penjadwalan = Penjadwalan::select('id_block')->where('id',$id_jadwal)->first();

                              // JIKA 0, ARTINYA BELUM ABSEN
                          if ($query_cek_presensi == 0) {

                        // INSERT KE TABLE PRESENSI
                            $presensi = Presensi::create([
                          'id_user' => $id_dosen->id,// ID USER DOSEN
                          'id_jadwal' => $id_jadwal,// ID JADWAL
                          'id_ruangan' => $id_ruangan,// ID JADWAL
                          'longitude' => $longitude,// LONGITUDE
                          'latitude' => $latitude,// LATITUDE
                          'jarak_ke_lokasi_absen' => $jarak_ke_lokasi_absen, // JARAK KE LOKASI ABSEN
                          'id_block'  =>  $query_penjadwalan->id_block
                        ]);

                          // MEMBUAT NAMA FILE DENGAN EXTENSI PNG 
                            $filename = 'image' . DIRECTORY_SEPARATOR . str_random(40) . '.png';

                          // UPLOAD FOTO
                            file_put_contents($filename,base64_decode($image));

                          // INSERT FOTO KE TABLE PRSENSI   
                            $presensi->foto = $filename;     
                            $presensi->save();  


                            $penjadwalan_terlaksana = Penjadwalan::where("id",$id_jadwal)->update(["status_jadwal" => 1]);
                                   // update Penjadwalan (status jadwal di set = 1 atau "TERLAKSANA") where id_jadwal dosen = $id jadwal dosen

                            $jadwal_dosen_terlaksana = Jadwal_dosen::where("id_jadwal",$id_jadwal)->update(["status_jadwal" => 1]);
                                  // update jadwal dosen (status jadwal di set = 1 atau "TERLAKSANA") where id_jadwal dosen = $id jadwal dosen



                          $response["value"] = 1;// RESPONSE VALUE 1
                          $response["message"] = "Berhasil Absen";// RESPONSE BERHASIL ABSEN
                          // DATA DIKEMBALIKAN DALAM BENTUK JSON
                          return  json_encode($response);


                    }else{// JIKA TIDAK NOL, MAKA DOSEN SUDAH ABSEN


                          $response["value"] = 2;// RESPONSE VALUE 0
                          $response["message"] = "Gagal Absen";// RESPONSE Gagal ABSEN
                          // DATA DIKEMBALIKAN DALAM BENTUK JSON
                          return  json_encode($response);

                    }// END          

                  }else{

                          $response["value"] = 3;// RESPONSE VALUE 0
                          $response["message"] = "Gagal Absen, Jadwal belum dimulai";// RESPONSE Gagal ABSEN
                          // DATA DIKEMBALIKAN DALAM BENTUK JSON
                          return  json_encode($response);
                        }




    }// PRESENSI

//MAHASISWA
    public function ubah_password_dosen (Request $request){

      $dosen = $request->username;// DOSEN YANG LOGIN
      $id_dosen = User::select('id')->where('email',$dosen)->first();//  AMBIL ID DOSEN
      
      $password_lama = $request->password_lama;// PASSWORD LAMA
      $username_baru = $request->username_baru;// USERNAME BARU
      $password_baru = $request->password_baru;// PASSWORD BARU


      // jika username dosen == username baru  atau maksudnya username tidak diedit
      if ($dosen == $username_baru) {// maka

                // CEK USER DAN PASSWORD LAMA
        if (Auth::attempt(['email' => $dosen, 'password' => $password_lama])) {

                  // UPDATE USER SET PASSWORD
          $update_user = User::where("id",$id_dosen->id)->update(["password" => bcrypt($password_baru)]);


                    $response["value"] = 1;// RESPONSE VALUE 0
                    $response["message"] = "Password Berhasil Di Ubah";// RESPONSE Gagal ABSEN
                                  // DATA DIKEMBALIKAN DALAM BENTUK JSON
                    return  json_encode($response);

                  }else{

                      $response["value"] = 0;// RESPONSE VALUE 0
                    $response["message"] = "Mohon Maaf Password Lama Anda Salah";// RESPONSE Gagal ABSEN
                                  // DATA DIKEMBALIKAN DALAM BENTUK JSON

                    return json_encode($response);

                  } 


      }// jika tidak
      else{
              $cek_username = User::where('email',$username_baru)->count();// cek username baru

                    // jika username baru tidak ada yang sama dengan username./email yang lain
              if ($cek_username == 0) {
                                // CEK USERNAME DAN PASSWPRD LAMA
                if (Auth::attempt(['email' => $dosen, 'password' => $password_lama])) {

                  $update_user = User::where("id",$id_dosen->id)->update(["email" => $username_baru, "password" => bcrypt($password_baru)]);


                                $response["value"] = 1;// RESPONSE VALUE 0
                                $response["message"] = "Password Berhasil Di Ubah";// RESPONSE Gagal ABSEN
                                              // DATA DIKEMBALIKAN DALAM BENTUK JSON
                                return  json_encode($response);

                              }else{

                                  $response["value"] = 0;// RESPONSE VALUE 0
                                $response["message"] = "Mohon Maaf Password Lama Anda Salah";// RESPONSE Gagal ABSEN
                                              // DATA DIKEMBALIKAN DALAM BENTUK JSON

                                return json_encode($response);

                              } 

                       // jika tidak,         
                            }else{

                                $response["value"] = 2;// RESPONSE VALUE 0
                                $response["message"] = "Mohon Maaf, Username atau Email anda sudah ada";// RESPONSE Gagal ABSEN
                                              // DATA DIKEMBALIKAN DALAM BENTUK JSON

                                return json_encode($response);
                              }
                            }

                          }

                          public function cek_profile_dosen(Request $request){

      $dosen = User::select('foto_profil','name','no_hp')->where('email',$request->user)->first();//  AMBIL ID DOSEN
      $result = array();

      if ($dosen->foto_profil == '' || $dosen->foto_profil == "NULL") {

            $value = 0;// value = 0


          }else{

            $value = 1;// value = 1

          }

          array_push($result,array('foto_profile' => $dosen->foto_profil, 'nama_dosen'=> $dosen->name , 'no_telp' => $dosen->no_hp));


          return json_encode(array('value' => $value , 'result'=>$result));

        }

        public function update_profile_dosen(Request $request)
        {

        $image = $request->image; // FOTO PROFILE

        $dosen = User::select('foto_profil')->where('email',$request->user)->first();//  AMBIL FOTO PROFILE
        $filepath = $dosen->foto_profil;          

                // MEMBUAT NAMA FILE DENGAN EXTENSI PNG 
        $filename = 'image' . DIRECTORY_SEPARATOR . str_random(40) . '.png';

                              // UPLOAD FOTO
        file_put_contents($filename,base64_decode($image));
              // UPDATE FOTO
        $user = User::where('email',$request->user)->update(["foto_profil" => $filename]);

        // JIKA QUERY BERHASIL DI EKSKUSI
        if ($user == TRUE) {

                    try {// hapus foto lama
                      File::delete($filepath);
                    } catch (FileNotFoundException $e) {
                    // File sudah dihapus/tidak ada
                    }

            $response["value"] = 1;// value = 1
            $response["message"] = "Berhasil"; //  berhasil
          }else{
            $response["value"] = 0;// value = 1
            $response["message"] = "Foto Gagal Di Ubah"; // GAGAL
          }

                return  json_encode($response);// data yang dikembalikan berupa json


              }

    //LOGIN ABSEN MAHASISWA
              public function login_mahasiswa_android(Request $request){

      if (Auth::attempt(['email' => $request->username, 'password' => $request->password]) && Auth::user()->status) { // Authentication passed...

        $user_otoritas = Auth::user()->roles->first()->name; // cek otoritas

          if ($user_otoritas == 'mahasiswa') { // jika otoritas nya mahasiswa maka login akan berhasil
            $response["value"] = 1;// value = 1
            $response["message"] = "Login Berhasil"; // login berhasil
          }
          else{
            $response["value"] = 2;// value = 2
            $response["message"] = "Login Gagal, Anda Bukan Mahasiswa!!";// login gagal, kerena user bukan mahasiswa
          }

        }
        else {
            $response["value"] = 3;// value = 3
            $response["message"] = "Anda Tidak Bisa Login Di Karenakan Belum Di Konfirmasi Oleh Admin atau Username dan Password Anda Salah";// login gagal            
          }

      return  json_encode($response);// data yang dikembalikan berupa json

    }
    //LOGIN ABSEN MAHASISWA

    //DAFTAR JADWAL MAHASISWA
    public function list_jadwal_mahasiswa(Request $request){

        $mahasiswa = Auth::user()->email;// MAHASISWA YANG LOGIN
        $data_mahasiswa = User::select(['id', 'id_angkatan'])->where('email',$mahasiswa)->first();//  AMBIL ID MAHASISWA dan ANGKATAN
        $data_kelompok = ListKelompokMahasiswa::where('id_mahasiswa',$data_mahasiswa->id)->get();
//SELECT SEMUA ID BLOCK BERDASARKAN ANGKATAN USER LOGIN
        $data_block = Master_block::select('master_blocks.id')
        ->leftJoin('mahasiswa_block', 'mahasiswa_block.id_block', '=', 'master_blocks.id')
        ->where('mahasiswa_block.id_mahasiswa',$data_mahasiswa->id)
        ->orWhere('master_blocks.id_angkatan',$data_mahasiswa->id_angkatan)->get();

        $value = 0;
        $result = array();// ARRAY RESULT
        $array_block = array();
        foreach($data_block as $data_blocks) {
          array_push($array_block, $data_blocks->id);
        }

        /*untuk mendapatkan jadwal yang csl dan tutor 
          karena csl & tutor itu mahasiswa nya per kelompok bukan perangkatan seperti block
        */
          $array_kelompok = array();      
          foreach($data_kelompok as $data_kelompoks) {
            array_push($array_kelompok, $data_kelompoks->id_kelompok_mahasiswa);
          }

          $hari_ini = date("Y-m-d");

        //penjadwalan kuliah dan praktikum
          $penjadwalans = Penjadwalan::jadwalBlockMahasiswa($hari_ini,$array_block)
          ->get();

      //mata kuliah 
      foreach ($penjadwalans as $list_jadwal_mahasiswa) {// FOREACH
        if ($list_jadwal_mahasiswa['nama_mata_kuliah'] == "") {
          $mata_kuliah = "-";
        }
        else{
          $mata_kuliah = $list_jadwal_mahasiswa['nama_mata_kuliah'];
        }

        $value = $value + 1;
        //array push penjadwalan kuliah dan praktikum
        array_push($result, 
                  array('tanggal' => $this->tanggal_terbalik($list_jadwal_mahasiswa['tanggal']),// TANGGAL DI FORMAT=> Y/M/D
                        'waktu' => $list_jadwal_mahasiswa['waktu_mulai'] ." - " . $list_jadwal_mahasiswa['waktu_selesai'],// WAKTU MULAI DAN WAKTU SELESAI DIJADIKAN SATU STRING
                        'mata_kuliah' => $mata_kuliah,// MATA KULIAH
                        'tipe_jadwal' => $list_jadwal_mahasiswa['tipe_jadwal'],// MATA KULIAH
                        'nama_ruangan' => $list_jadwal_mahasiswa['ruangan'], // NAMA RUANGAN
                        'id_jadwal' => $list_jadwal_mahasiswa['id_jadwal'], // ID JADWAL
                        'id_ruangan' => $list_jadwal_mahasiswa['id_ruangan'], // ID RUANGAN
                        'latitude' => $list_jadwal_mahasiswa['latitude'], // LATITUDE
                        'longitude' => $list_jadwal_mahasiswa['longitude'], // LONGITUDE
                        'batas_jarak_absen' => $list_jadwal_mahasiswa['batas_jarak_absen'] // LONGITUDE


                        )// ARRAY
                  );// ARRAY PUSH

      }// endforeach penjadwalan kuliah praktikum

      //penjadwalan csl dan tutor
      $penjadwalans_csl_tutor = Penjadwalan::jadwalCslTutorMahasiswa($hari_ini,$array_kelompok)->get();

      foreach ($penjadwalans_csl_tutor as $list_jadwal_mahasiswa) {

        if ($list_jadwal_mahasiswa['materi'] == "") {
          $materi = "-";
        }
        else{
          $materi = $list_jadwal_mahasiswa['materi'];
        }

        $value = $value + 1;
        //array push penjadwalan kuliah dan praktikum
        array_push($result, 
                  array('tanggal' => $this->tanggal_terbalik($list_jadwal_mahasiswa['tanggal']),// TANGGAL DI FORMAT=> Y/M/D
                        'waktu' => $list_jadwal_mahasiswa['waktu_mulai'] ." - " . $list_jadwal_mahasiswa['waktu_selesai'],// WAKTU MULAI DAN WAKTU SELESAI DIJADIKAN SATU STRING
                        'mata_kuliah' => $materi,// MATA KULIAH
                        'tipe_jadwal' => $list_jadwal_mahasiswa['tipe_jadwal'],// MATA KULIAH
                        'nama_ruangan' => $list_jadwal_mahasiswa['ruangan'], // NAMA RUANGAN
                        'id_jadwal' => $list_jadwal_mahasiswa['id_jadwal'], // ID JADWAL
                        'id_ruangan' => $list_jadwal_mahasiswa['id_ruangan'], // ID RUANGAN
                        'latitude' => $list_jadwal_mahasiswa['latitude'], // LATITUDE
                        'longitude' => $list_jadwal_mahasiswa['longitude'], // LONGITUDE
                        'batas_jarak_absen' => $list_jadwal_mahasiswa['batas_jarak_absen'] // LONGITUDE


                        )// ARRAY
                  );// ARRAY PUSH

      } // endforeach penjadwalan csl dan tutor


     // DATA YANG DIKIRIM BERUPA JSON
      return json_encode(array(
                  'value' => $value , 'result'=>$result
               ));

    }
    //DAFTAR JADWAL MAHASISWA

    //PRESENSI MAHASISWA
    // presendi dosen
    public function presensi_mahasiswa(Request $request){

      $mahasiswa = $request->username;// MAHASISWA YANG LOGIN
      $id_mahasiswa = User::select('id')->where('email',$mahasiswa)->first();//  AMBIL ID MAHASISWA
      $id_jadwal = $request->id_jadwal;// ID JADWAL
      $id_ruangan = $request->id_ruangan; // ID RUANGAN
      $longitude = $request->longitude_sekarang;// LONGITUDE
      $latitude = $request->latitude_sekarang;// LATITUDE
      $image = $request->image; // FOTO ABSEN
      $jarak_ke_lokasi_absen = $request->jarak_ke_lokasi_absen; //JARAK LOKASI
      $waktu = date("Y-m-d H:i:s");
      $tanggal_db = $request->tanggal;
      $tanggal = Carbon::createFromFormat('d/m/Y', $tanggal_db)->format('Y-m-d');
      $waktu_jadwal = $request->waktu_jadwal;

      $waktu_jadwal_mahasiswa = explode(" - ", $waktu_jadwal); 
      $waktu_mulai = $waktu_jadwal_mahasiswa[0]; 
      $waktu_selesai = $waktu_jadwal_mahasiswa[1];

      $waktu_jadwal_mulai = $tanggal ." ". $waktu_mulai;      
      $waktu_jadwal_selesai = $tanggal ." ". $waktu_selesai; 

      $data_block = Penjadwalan::select(['id', 'id_block'])->where('id', $id_jadwal)->first();

      if ($waktu >= $waktu_jadwal_mulai AND $waktu <= $waktu_jadwal_selesai) {

      // CEK APAKAH MAHASISWA INI SUDAH ABSEN BELUM UNTUK JADWAL INI
      $query_cek_presensi = PresensiMahasiswa::where('id_jadwal',$id_jadwal) // WHERE ID JADWAL
                          ->where('id_user',$id_mahasiswa->id)// AND ID MAHASISWA
                          ->count();

                  // JIKA 0, ARTINYA BELUM ABSEN
                          if ($query_cek_presensi == 0) {

                        // INSERT KE TABLE PRESENSI
                            $presensi = PresensiMahasiswa::create([
                          'id_user' => $id_mahasiswa->id,// ID USER MAHASISWA
                          'id_jadwal' => $id_jadwal,// ID JADWAL
                          'id_ruangan' => $id_ruangan,// ID JADWAL
                          'longitude' => $longitude,// LONGITUDE
                          'latitude' => $latitude,// LATITUDE
                          'jarak_ke_lokasi_absen' => $jarak_ke_lokasi_absen,// JARAK LOKASI
                          'id_block' => $data_block->id_block,//ID BLOCK
                        ]);

                          // MEMBUAT NAMA FILE DENGAN EXTENSI PNG 
                            $filename = 'image' . DIRECTORY_SEPARATOR . str_random(40) . '.png';

                          // UPLOAD FOTO
                            file_put_contents($filename,base64_decode($image));

                          // INSERT FOTO KE TABLE PRSENSI   
                            $presensi->foto = $filename;     
                            $presensi->save();

                          $response["value"] = 1;// RESPONSE VALUE 1
                          $response["message"] = "Berhasil Absen";// RESPONSE BERHASIL ABSEN     
                        }
                    else{// JIKA TIDAK NOL, MAKA MAHASISWA SUDAH ABSEN

                          $response["value"] = 2;// RESPONSE VALUE 0
                          $response["message"] = "Gagal Absen";// RESPONSE Gagal ABSEN
                          // DATA DIKEMBALIKAN DALAM BENTUK JSON
                          return  json_encode($response);

                    }// END
                  }
                  else{
                          $response["value"] = 3;// RESPONSE VALUE 0
                          $response["message"] = "Gagal Absen, Jadwal Belum Dimulai";// RESPONSE Gagal ABSEN
                        }

                        return  json_encode($response);
      //PRESENSI MAHASISWA
                      }


// CARI JADWAL MAHSISWA
                      public function search_jadwal_mahasiswa(Request $request){

        $search = $request->search;// REQUEST SEARCH
        $mahasiswa = Auth::user()->email;// MAHASISWA YANG LOGIN
        $data_mahasiswa = User::select(['id', 'id_angkatan'])->where('email',$mahasiswa)->first();//  AMBIL ID MAHASISWA

//SELECT SEMUA ID BLOCK BERDASARKAN ANGKATAN USER LOGIN
        $data_block = Master_block::select('master_blocks.id')
        ->leftJoin('mahasiswa_block', 'mahasiswa_block.id_block', '=', 'master_blocks.id')
        ->where('mahasiswa_block.id_mahasiswa',$data_mahasiswa->id)
        ->orWhere('master_blocks.id_angkatan',$data_mahasiswa->id_angkatan)->get();

        $data_kelompok = ListKelompokMahasiswa::where('id_mahasiswa',$data_mahasiswa->id)->get();
    /*untuk mendapatkan jadwal yang csl dan tutor 
          karena csl & tutor itu mahasiswa nya per kelompok bukan perangkatan seperti block
        */
          $array_kelompok = array();      
          foreach($data_kelompok as $data_kelompoks) {
            array_push($array_kelompok, $data_kelompoks->id_kelompok_mahasiswa);
          }

          $value = 0;
        $result = array();// ARRAY RESULT
        $waktu = date("Y-m-d H:i:s");
        $hari_ini = date("Y-m-d");

        $array_block = array();
        foreach ($data_block as $data_blocks) {
          array_push($array_block, $data_blocks->id);
        }

        //penjadwalan kuliah dan praktikum
        $penjadwalans = Penjadwalan::jadwalBlockMahasiswa($hari_ini,$array_block)->searchJadwal($search)->get();

      foreach ($penjadwalans as $list_jadwal_mahasiswa) {// FOREACH
        if ($list_jadwal_mahasiswa['nama_mata_kuliah'] == "") {
          $mata_kuliah = "-";
        }
        else{
          $mata_kuliah = $list_jadwal_mahasiswa['nama_mata_kuliah'];
        }

        $value = $value + 1;
        //ARRAY PUSH
        array_push($result, 
                  array('tanggal' => $this->tanggal_terbalik($list_jadwal_mahasiswa['tanggal']),// TANGGAL DI FORMAT=> Y/M/D
                        'waktu' => $list_jadwal_mahasiswa['waktu_mulai'] ." - " . $list_jadwal_mahasiswa['waktu_selesai'],// WAKTU MULAI DAN WAKTU SELESAI DIJADIKAN SATU STRING
                        'mata_kuliah' => $mata_kuliah,// MATA KULIAH
                        'tipe_jadwal' => $list_jadwal_mahasiswa['tipe_jadwal'],// MATA KULIAH
                        'nama_ruangan' => $list_jadwal_mahasiswa['ruangan'], // NAMA RUANGAN
                        'id_jadwal' => $list_jadwal_mahasiswa['id_jadwal'], // ID JADWAL
                        'id_ruangan' => $list_jadwal_mahasiswa['id_ruangan'], // ID RUANGAN
                        'latitude' => $list_jadwal_mahasiswa['latitude'], // LATITUDE
                        'longitude' => $list_jadwal_mahasiswa['longitude'], // LONGITUDE
                        'batas_jarak_absen' => $list_jadwal_mahasiswa['batas_jarak_absen'] // LONGITUDE


                        )// ARRAY
                  );// ARRAY PUSH

      }// END FOREACH penjadwalan kuliah dan praktikum

      //penjadwalan csl dan tutor
      $penjadwalans_csl_tutor = Penjadwalan::jadwalCslTutorMahasiswa($hari_ini,$array_kelompok)->searchJadwal($search)->get();

      foreach ($penjadwalans_csl_tutor as $list_jadwal_mahasiswa) {

        if ($list_jadwal_mahasiswa['materi'] == "") {
          $materi = "-";
        }
        else{
          $materi = $list_jadwal_mahasiswa['materi'];
        }

        $value = $value + 1;
        //array push penjadwalan kuliah dan praktikum
        array_push($result, 
                  array('tanggal' => $this->tanggal_terbalik($list_jadwal_mahasiswa['tanggal']),// TANGGAL DI FORMAT=> Y/M/D
                        'waktu' => $list_jadwal_mahasiswa['waktu_mulai'] ." - " . $list_jadwal_mahasiswa['waktu_selesai'],// WAKTU MULAI DAN WAKTU SELESAI DIJADIKAN SATU STRING
                        'mata_kuliah' => $materi,// MATA KULIAH
                        'tipe_jadwal' => $list_jadwal_mahasiswa['tipe_jadwal'],// MATA KULIAH
                        'nama_ruangan' => $list_jadwal_mahasiswa['ruangan'], // NAMA RUANGAN
                        'id_jadwal' => $list_jadwal_mahasiswa['id_jadwal'], // ID JADWAL
                        'id_ruangan' => $list_jadwal_mahasiswa['id_ruangan'], // ID RUANGAN
                        'latitude' => $list_jadwal_mahasiswa['latitude'], // LATITUDE
                        'longitude' => $list_jadwal_mahasiswa['longitude'], // LONGITUDE
                        'batas_jarak_absen' => $list_jadwal_mahasiswa['batas_jarak_absen'] // LONGITUDE


                        )// ARRAY
                  );// ARRAY PUSH

      } // endforeach penjadwalan csl dan tutor




      // DATA YANG DIKEMBALIKAN  BERUPA JSON
      return json_encode(array('value' => '1' , 'result'=>$result));


    }
// END CARI JADWAL MAHSISWA

    //DAFTAR JADWAL MAHASISWA BESOK
    public function jadwal_besok(Request $request){

        $mahasiswa = Auth::user()->email;// MAHASISWA YANG LOGIN
        $data_mahasiswa = User::select(['id', 'id_angkatan'])->where('email',$mahasiswa)->first();//  AMBIL ID MAHASISWA
        $data_block = Master_block::select('id')->where('id_angkatan',$data_mahasiswa->id_angkatan)->get();
        $value = 0;
        $result = array();// ARRAY RESULT
        $waktu = date("Y-m-d H:i:s");
        $hari_besok = mktime (0,0,0, date("m"), date("d")+1,date("Y"));
        $tanggal_besok = date('Y-m-d',$hari_besok );// TANGGAL BESOK

        $array_block = array();
        foreach ($data_block as $data_blocks) {
          array_push($array_block, $data_blocks->id);
        }

      //penjadwalan kuliah praktikum pleno
        $penjadwalans = Penjadwalan::jadwalBlockMahasiswa($tanggal_besok,$array_block)->get();


      foreach ($penjadwalans as $list_jadwal_mahasiswa) {// FOREACH
        if ($list_jadwal_mahasiswa['nama_mata_kuliah'] == "") {
          $mata_kuliah = "-";
        }
        else{
          $mata_kuliah = $list_jadwal_mahasiswa['nama_mata_kuliah'];
        }

        $value = $value + 1;
        //ARRAY PUSH
        array_push($result, 
                  array('tanggal' => $this->tanggal_terbalik($list_jadwal_mahasiswa['tanggal']),// TANGGAL DI FORMAT=> Y/M/D
                        'waktu' => $list_jadwal_mahasiswa['waktu_mulai'] ." - " . $list_jadwal_mahasiswa['waktu_selesai'],// WAKTU MULAI DAN WAKTU SELESAI DIJADIKAN SATU STRING
                        'mata_kuliah' => $mata_kuliah,// MATA KULIAH
                        'tipe_jadwal' => $list_jadwal_mahasiswa['tipe_jadwal'],// MATA KULIAH
                        'nama_ruangan' => $list_jadwal_mahasiswa['ruangan'], // NAMA RUANGAN
                        'id_jadwal' => $list_jadwal_mahasiswa['id_jadwal'], // ID JADWAL
                        'id_ruangan' => $list_jadwal_mahasiswa['id_ruangan'], // ID RUANGAN
                        'latitude' => $list_jadwal_mahasiswa['latitude'], // LATITUDE
                        'longitude' => $list_jadwal_mahasiswa['longitude'], // LONGITUDE
                        'batas_jarak_absen' => $list_jadwal_mahasiswa['batas_jarak_absen'] // LONGITUDE


                        )// ARRAY
                  );// ARRAY PUSH

      }// END FOREACH

      $data_kelompok = ListKelompokMahasiswa::where('id_mahasiswa',$data_mahasiswa->id)->get();
    /*untuk mendapatkan jadwal yang csl dan tutor 
          karena csl & tutor itu mahasiswa nya per kelompok bukan perangkatan seperti block
        */
          $array_kelompok = array();      
          foreach($data_kelompok as $data_kelompoks) {
            array_push($array_kelompok, $data_kelompoks->id_kelompok_mahasiswa);
          }


      //penjadwalan csl dan tutor
          $penjadwalans_csl_tutor = Penjadwalan::jadwalCslTutorMahasiswa($tanggal_besok,$array_kelompok)->get();

          foreach ($penjadwalans_csl_tutor as $list_jadwal_mahasiswa) {

            if ($list_jadwal_mahasiswa['materi'] == "") {
              $materi = "-";
            }
            else{
              $materi = $list_jadwal_mahasiswa['materi'];
            }

            $value = $value + 1;
        //array push penjadwalan kuliah dan praktikum
            array_push($result, 
                  array('tanggal' => $this->tanggal_terbalik($list_jadwal_mahasiswa['tanggal']),// TANGGAL DI FORMAT=> Y/M/D
                        'waktu' => $list_jadwal_mahasiswa['waktu_mulai'] ." - " . $list_jadwal_mahasiswa['waktu_selesai'],// WAKTU MULAI DAN WAKTU SELESAI DIJADIKAN SATU STRING
                        'mata_kuliah' => $materi,// MATA KULIAH
                        'tipe_jadwal' => $list_jadwal_mahasiswa['tipe_jadwal'],// MATA KULIAH
                        'nama_ruangan' => $list_jadwal_mahasiswa['ruangan'], // NAMA RUANGAN
                        'id_jadwal' => $list_jadwal_mahasiswa['id_jadwal'], // ID JADWAL
                        'id_ruangan' => $list_jadwal_mahasiswa['id_ruangan'], // ID RUANGAN
                        'latitude' => $list_jadwal_mahasiswa['latitude'], // LATITUDE
                        'longitude' => $list_jadwal_mahasiswa['longitude'], // LONGITUDE
                        'batas_jarak_absen' => $list_jadwal_mahasiswa['batas_jarak_absen'] // LONGITUDE


                        )// ARRAY
                  );// ARRAY PUSH

      } // endforeach penjadwalan csl dan tutor

     // DATA YANG DIKIRIM BERUPA JSON
      return json_encode(array('value' => $value , 'result'=>$result));

    }
    //DAFTAR JADWAL MAHASISWA BESOK


// CARI JADWAL MAHSISWA BESOK
    public function search_jadwal_mahasiswa_besok(Request $request){

        $search = $request->search;// REQUEST SEARCH
        $mahasiswa = Auth::user()->email;// MAHASISWA YANG LOGIN
        $data_mahasiswa = User::select(['id', 'id_angkatan'])->where('email',$mahasiswa)->first();//  AMBIL ID MAHASISWA
        $data_block = Master_block::select('id')->where('id_angkatan',$data_mahasiswa->id_angkatan)->get();

        $value = 0;
        $result = array();// ARRAY RESULT
        $waktu = date("Y-m-d H:i:s");
        $hari_besok = mktime (0,0,0, date("m"), date("d")+1,date("Y"));
        $tanggal_besok = date('Y-m-d',$hari_besok );// TANGGAL BESOK

        $array_block = array();
        foreach ($data_block as $data_blocks) {
          array_push($array_block, $data_blocks->id);
        }

      //penjadwalan kuliah praktikum pleno
        $penjadwalans = Penjadwalan::jadwalBlockMahasiswa($tanggal_besok,$array_block)->searchJadwal($search)->get();


      foreach ($penjadwalans as $list_jadwal_mahasiswa) {// FOREACH
        if ($list_jadwal_mahasiswa['nama_mata_kuliah'] == "") {
          $mata_kuliah = "-";
        }
        else{
          $mata_kuliah = $list_jadwal_mahasiswa['nama_mata_kuliah'];
        }

        $value = $value + 1;
        //ARRAY PUSH
        array_push($result, 
                  array('tanggal' => $this->tanggal_terbalik($list_jadwal_mahasiswa['tanggal']),// TANGGAL DI FORMAT=> Y/M/D
                        'waktu' => $list_jadwal_mahasiswa['waktu_mulai'] ." - " . $list_jadwal_mahasiswa['waktu_selesai'],// WAKTU MULAI DAN WAKTU SELESAI DIJADIKAN SATU STRING
                        'mata_kuliah' => $mata_kuliah,// MATA KULIAH
                        'tipe_jadwal' => $list_jadwal_mahasiswa['tipe_jadwal'],// MATA KULIAH
                        'nama_ruangan' => $list_jadwal_mahasiswa['ruangan'], // NAMA RUANGAN
                        'id_jadwal' => $list_jadwal_mahasiswa['id_jadwal'], // ID JADWAL
                        'id_ruangan' => $list_jadwal_mahasiswa['id_ruangan'], // ID RUANGAN
                        'latitude' => $list_jadwal_mahasiswa['latitude'], // LATITUDE
                        'longitude' => $list_jadwal_mahasiswa['longitude'], // LONGITUDE
                        'batas_jarak_absen' => $list_jadwal_mahasiswa['batas_jarak_absen'] // LONGITUDE


                        )// ARRAY
                  );// ARRAY PUSH

      }// END FOREACH penjadwalan kuliah praktikum pleno

      $data_kelompok = ListKelompokMahasiswa::where('id_mahasiswa',$data_mahasiswa->id)->get();
    /*untuk mendapatkan jadwal yang csl dan tutor 
          karena csl & tutor itu mahasiswa nya per kelompok bukan perangkatan seperti block
        */
          $array_kelompok = array();      
          foreach($data_kelompok as $data_kelompoks) {
            array_push($array_kelompok, $data_kelompoks->id_kelompok_mahasiswa);
          }


      //penjadwalan csl dan tutor
          $penjadwalans_csl_tutor = Penjadwalan::jadwalCslTutorMahasiswa($tanggal_besok,$array_kelompok)->searchJadwal($search)->get();

          foreach ($penjadwalans_csl_tutor as $list_jadwal_mahasiswa) {

            if ($list_jadwal_mahasiswa['materi'] == "") {
              $materi = "-";
            }
            else{
              $materi = $list_jadwal_mahasiswa['materi'];
            }

            $value = $value + 1;
        //array push penjadwalan kuliah dan praktikum
            array_push($result, 
                  array('tanggal' => $this->tanggal_terbalik($list_jadwal_mahasiswa['tanggal']),// TANGGAL DI FORMAT=> Y/M/D
                        'waktu' => $list_jadwal_mahasiswa['waktu_mulai'] ." - " . $list_jadwal_mahasiswa['waktu_selesai'],// WAKTU MULAI DAN WAKTU SELESAI DIJADIKAN SATU STRING
                        'mata_kuliah' => $materi,// MATA KULIAH
                        'tipe_jadwal' => $list_jadwal_mahasiswa['tipe_jadwal'],// MATA KULIAH
                        'nama_ruangan' => $list_jadwal_mahasiswa['ruangan'], // NAMA RUANGAN
                        'id_jadwal' => $list_jadwal_mahasiswa['id_jadwal'], // ID JADWAL
                        'id_ruangan' => $list_jadwal_mahasiswa['id_ruangan'], // ID RUANGAN
                        'latitude' => $list_jadwal_mahasiswa['latitude'], // LATITUDE
                        'longitude' => $list_jadwal_mahasiswa['longitude'], // LONGITUDE
                        'batas_jarak_absen' => $list_jadwal_mahasiswa['batas_jarak_absen'] // LONGITUDE


                        )// ARRAY
                  );// ARRAY PUSH

      } // endforeach penjadwalan csl dan tutor


      // DATA YANG DIKEMBALIKAN  BERUPA JSON
      return json_encode(array('value' => '1' , 'result'=>$result));


    }
// END CARI JADWAL MAHASISWA BESOK

    //DAFTAR JADWAL MAHASISWA LUSA
    public function jadwal_lusa(Request $request){

        $mahasiswa = Auth::user()->email;// MAHASISWA YANG LOGIN
        $data_mahasiswa = User::select(['id', 'id_angkatan'])->where('email',$mahasiswa)->first();//  AMBIL ID MAHASISWA
        $data_block = Master_block::select('id')->where('id_angkatan',$data_mahasiswa->id_angkatan)->get();
        $value = 0;
        $result = array();// ARRAY RESULT
        $waktu = date("Y-m-d H:i:s");
        $hari_lusa = mktime (0,0,0, date("m"), date("d")+2,date("Y"));
        $tanggal_lusa = date('Y-m-d',$hari_lusa );// TANGGAL LUSA

        $array_block = array();
        foreach ($data_block as $data_blocks) {
          array_push($array_block, $data_blocks->id);
        }

         //penjadwalan kuliah praktikum pleno
        $penjadwalans = Penjadwalan::jadwalBlockMahasiswa($tanggal_lusa,$array_block)->get();


      foreach ($penjadwalans as $list_jadwal_mahasiswa) {// FOREACH
        if ($list_jadwal_mahasiswa['nama_mata_kuliah'] == "") {
          $mata_kuliah = "-";
        }
        else{
          $mata_kuliah = $list_jadwal_mahasiswa['nama_mata_kuliah'];
        }

        $value = $value + 1;
        //ARRAY PUSH
        array_push($result, 
                  array('tanggal' => $this->tanggal_terbalik($list_jadwal_mahasiswa['tanggal']),// TANGGAL DI FORMAT=> Y/M/D
                        'waktu' => $list_jadwal_mahasiswa['waktu_mulai'] ." - " . $list_jadwal_mahasiswa['waktu_selesai'],// WAKTU MULAI DAN WAKTU SELESAI DIJADIKAN SATU STRING
                        'mata_kuliah' => $mata_kuliah,// MATA KULIAH
                        'tipe_jadwal' => $list_jadwal_mahasiswa['tipe_jadwal'],// MATA KULIAH
                        'nama_ruangan' => $list_jadwal_mahasiswa['ruangan'], // NAMA RUANGAN
                        'id_jadwal' => $list_jadwal_mahasiswa['id_jadwal'], // ID JADWAL
                        'id_ruangan' => $list_jadwal_mahasiswa['id_ruangan'], // ID RUANGAN
                        'latitude' => $list_jadwal_mahasiswa['latitude'], // LATITUDE
                        'longitude' => $list_jadwal_mahasiswa['longitude'], // LONGITUDE
                        'batas_jarak_absen' => $list_jadwal_mahasiswa['batas_jarak_absen'] // LONGITUDE


                        )// ARRAY
                  );// ARRAY PUSH

      }// END FOREACH penjadwalan kuliah , praktikum dan pleno

      $data_kelompok = ListKelompokMahasiswa::where('id_mahasiswa',$data_mahasiswa->id)->get();
    /*untuk mendapatkan jadwal yang csl dan tutor 
          karena csl & tutor itu mahasiswa nya per kelompok bukan perangkatan seperti block
        */
          $array_kelompok = array();      
          foreach($data_kelompok as $data_kelompoks) {
            array_push($array_kelompok, $data_kelompoks->id_kelompok_mahasiswa);
          }


      //penjadwalan csl dan tutor
          $penjadwalans_csl_tutor = Penjadwalan::jadwalCslTutorMahasiswa($tanggal_lusa,$array_kelompok)->get();

          foreach ($penjadwalans_csl_tutor as $list_jadwal_mahasiswa) {

            if ($list_jadwal_mahasiswa['materi'] == "") {
              $materi = "-";
            }
            else{
              $materi = $list_jadwal_mahasiswa['materi'];
            }

            $value = $value + 1;
        //array push penjadwalan kuliah dan praktikum
            array_push($result, 
                  array('tanggal' => $this->tanggal_terbalik($list_jadwal_mahasiswa['tanggal']),// TANGGAL DI FORMAT=> Y/M/D
                        'waktu' => $list_jadwal_mahasiswa['waktu_mulai'] ." - " . $list_jadwal_mahasiswa['waktu_selesai'],// WAKTU MULAI DAN WAKTU SELESAI DIJADIKAN SATU STRING
                        'mata_kuliah' => $materi,// MATA KULIAH
                        'tipe_jadwal' => $list_jadwal_mahasiswa['tipe_jadwal'],// MATA KULIAH
                        'nama_ruangan' => $list_jadwal_mahasiswa['ruangan'], // NAMA RUANGAN
                        'id_jadwal' => $list_jadwal_mahasiswa['id_jadwal'], // ID JADWAL
                        'id_ruangan' => $list_jadwal_mahasiswa['id_ruangan'], // ID RUANGAN
                        'latitude' => $list_jadwal_mahasiswa['latitude'], // LATITUDE
                        'longitude' => $list_jadwal_mahasiswa['longitude'], // LONGITUDE
                        'batas_jarak_absen' => $list_jadwal_mahasiswa['batas_jarak_absen'] // LONGITUDE


                        )// ARRAY
                  );// ARRAY PUSH

      } // endforeach penjadwalan csl dan tutor




     // DATA YANG DIKIRIM BERUPA JSON
      return json_encode(array('value' => $value , 'result'=>$result));

    }
    //DAFTAR JADWAL MAHASISWA LUSA


// CARI JADWAL MAHASISWA LUSA
    public function search_jadwal_mahasiswa_lusa(Request $request){

        $search = $request->search;// REQUEST SEARCH
        $mahasiswa = Auth::user()->email;// MAHASISWA YANG LOGIN
        $data_mahasiswa = User::select(['id', 'id_angkatan'])->where('email',$mahasiswa)->first();//  AMBIL ID MAHASISWA
        $data_block = Master_block::select('id')->where('id_angkatan',$data_mahasiswa->id_angkatan)->get();
        $value = 0;
        $result = array();// ARRAY RESULT
        $waktu = date("Y-m-d H:i:s");
        $hari_lusa = mktime (0,0,0, date("m"), date("d")+2,date("Y"));
        $tanggal_lusa = date('Y-m-d',$hari_lusa );// TANGGAL LUSA

        $array_block = array();
        foreach ($data_block as $data_blocks) {
          array_push($array_block, $data_blocks->id);
        }

             //penjadwalan kuliah praktikum pleno
        $penjadwalans = Penjadwalan::jadwalBlockMahasiswa($tanggal_lusa,$array_block)->searchJadwal($search)->get();

      foreach ($penjadwalans as $list_jadwal_mahasiswa) {// FOREACH
        if ($list_jadwal_mahasiswa['nama_mata_kuliah'] == "") {
          $mata_kuliah = "-";
        }
        else{
          $mata_kuliah = $list_jadwal_mahasiswa['nama_mata_kuliah'];
        }

        $value = $value + 1;
        //ARRAY PUSH
        array_push($result, 
                  array('tanggal' => $this->tanggal_terbalik($list_jadwal_mahasiswa['tanggal']),// TANGGAL DI FORMAT=> Y/M/D
                        'waktu' => $list_jadwal_mahasiswa['waktu_mulai'] ." - " . $list_jadwal_mahasiswa['waktu_selesai'],// WAKTU MULAI DAN WAKTU SELESAI DIJADIKAN SATU STRING
                        'mata_kuliah' => $mata_kuliah,// MATA KULIAH
                        'tipe_jadwal' => $list_jadwal_mahasiswa['tipe_jadwal'],// MATA KULIAH
                        'nama_ruangan' => $list_jadwal_mahasiswa['ruangan'], // NAMA RUANGAN
                        'id_jadwal' => $list_jadwal_mahasiswa['id_jadwal'], // ID JADWAL
                        'id_ruangan' => $list_jadwal_mahasiswa['id_ruangan'], // ID RUANGAN
                        'latitude' => $list_jadwal_mahasiswa['latitude'], // LATITUDE
                        'longitude' => $list_jadwal_mahasiswa['longitude'], // LONGITUDE
                        'batas_jarak_absen' => $list_jadwal_mahasiswa['batas_jarak_absen'] // LONGITUDE


                        )// ARRAY
                  );// ARRAY PUSH

      }// END FOREACH penjadwalan kuliah , praktikum dan pleno


      $data_kelompok = ListKelompokMahasiswa::where('id_mahasiswa',$data_mahasiswa->id)->get();
    /*untuk mendapatkan jadwal yang csl dan tutor 
          karena csl & tutor itu mahasiswa nya per kelompok bukan perangkatan seperti block
        */
          $array_kelompok = array();      
          foreach($data_kelompok as $data_kelompoks) {
            array_push($array_kelompok, $data_kelompoks->id_kelompok_mahasiswa);
          }

       //penjadwalan csl dan tutor
          $penjadwalans_csl_tutor = Penjadwalan::jadwalCslTutorMahasiswa($tanggal_lusa,$array_kelompok)->searchJadwal($search)->get();

          foreach ($penjadwalans_csl_tutor as $list_jadwal_mahasiswa) {

            if ($list_jadwal_mahasiswa['materi'] == "") {
              $materi = "-";
            }
            else{
              $materi = $list_jadwal_mahasiswa['materi'];
            }

            $value = $value + 1;
        //array push penjadwalan kuliah dan praktikum
            array_push($result, 
                  array('tanggal' => $this->tanggal_terbalik($list_jadwal_mahasiswa['tanggal']),// TANGGAL DI FORMAT=> Y/M/D
                        'waktu' => $list_jadwal_mahasiswa['waktu_mulai'] ." - " . $list_jadwal_mahasiswa['waktu_selesai'],// WAKTU MULAI DAN WAKTU SELESAI DIJADIKAN SATU STRING
                        'mata_kuliah' => $materi,// MATA KULIAH
                        'tipe_jadwal' => $list_jadwal_mahasiswa['tipe_jadwal'],// MATA KULIAH
                        'nama_ruangan' => $list_jadwal_mahasiswa['ruangan'], // NAMA RUANGAN
                        'id_jadwal' => $list_jadwal_mahasiswa['id_jadwal'], // ID JADWAL
                        'id_ruangan' => $list_jadwal_mahasiswa['id_ruangan'], // ID RUANGAN
                        'latitude' => $list_jadwal_mahasiswa['latitude'], // LATITUDE
                        'longitude' => $list_jadwal_mahasiswa['longitude'], // LONGITUDE
                        'batas_jarak_absen' => $list_jadwal_mahasiswa['batas_jarak_absen'] // LONGITUDE


                        )// ARRAY
                  );// ARRAY PUSH

      } // endforeach penjadwalan csl dan tutor

      // DATA YANG DIKEMBALIKAN  BERUPA JSON
      return json_encode(array('value' => '1' , 'result'=>$result));


    }
// END CARI JADWAL MAHASISWA LUSA


//UBAH PASSWORD MAHASISWA
    public function ubah_password_mahasiswa (Request $request){

      $mahasiswa = $request->username;// MAHASISWA YANG LOGIN
      $id_mahasiswa = User::select('id')->where('email',$mahasiswa)->first();//  AMBIL ID MAHASISWA

      $password_lama = $request->password_lama;
      $username_baru = $request->username_baru;
      $password_baru = $request->password_baru;

//JIKA USERNAME TIDAK DIUBAH -> UPDATE PASSWORD SAJA
      if ($mahasiswa == $username_baru) {

        if (Auth::attempt(['email' => $mahasiswa, 'password' => $password_lama])) {

          $update_user = User::where("id",$id_mahasiswa->id)->update(["email" => $username_baru, "password" => bcrypt($password_baru)]);

            $response["value"] = 1;// RESPONSE VALUE 1
            $response["message"] = "Password Berhasil Di Ubah";// RESPONSE BERHASIL ABSEN

            return  json_encode($response);

          }
          else{

            $response["value"] = 0;// RESPONSE VALUE 0
            $response["message"] = "Mohon Maaf Password Lama Anda Salah";// RESPONSE Gagal ABSEN

            return json_encode($response);
          } 
        }
//JIKA USERNAME DIUBAH -> UPDATE USERNAME DAN PASSWORD
        else{

      //CEK USERNAME SUDAH ADA ATAU BELUM
          $cek_username = User::where('email',$username_baru)->count();

      //JIKA USERNAME BELUM ADA DI AKUN LAIN
          if ($cek_username == 0) {

            if (Auth::attempt(['email' => $mahasiswa, 'password' => $password_lama])) {

              $update_user = User::where("id",$id_mahasiswa->id)->update(["email" => $username_baru, "password" => bcrypt($password_baru)]);

              $response["value"] = 1;// RESPONSE VALUE 1
              $response["message"] = "Password Berhasil Di Ubah";// RESPONSE BERHASIL ABSEN

              return  json_encode($response);

            }
            else{

              $response["value"] = 0;// RESPONSE VALUE 0
              $response["message"] = "Mohon Maaf Password Lama Anda Salah";// RESPONSE GAGAL ABSEN

              return json_encode($response);
            } 
          }
        //JIKA USERNAME SUDAH ADA DI AKUN LAIN
          else{

            $response["value"] = 2;// RESPONSE VALUE 0
            $response["message"] = "Mohon Maaf, Username Atau Email Anda Sudah Ada";// RESPONSE GAGAL ABSEN

            return json_encode($response);
          }

        }

      }
//END UBAH PASSWORD MAHASISWA

//CEK PROFIL MAHASISWA
      public function cek_profil_mahasiswa(Request $request){
      $user = Auth::user()->email;
      $mahasiswa = User::select('foto_profil','name','no_hp')->where('email',$user)->first();//  AMBIL ID MAHASISWA
      $result = array();

      if ($mahasiswa->foto_profil == '' || $mahasiswa->foto_profil == "NULL") {
        $value = 0;// value = 0
      }
      else{
        $value = 1;// value = 1
      }

      array_push($result,array('foto_profilnya' => $mahasiswa->foto_profil, 'nama_mahasiswa'=> $mahasiswa->name , 'no_telp' => $mahasiswa->no_hp));
      return json_encode(array('value' => $value , 'result'=>$result));
    }
  //CEK PROFIL MAHASISWA

  //UPDATE PROFIL MAHASISWA
    public function update_profil_mahasiswa(Request $request) {
      $image = $request->image; // FOTO PROFILE
      $mahasiswa = User::select('foto_profil')->where('email',$request->user)->first();//  AMBIL FOTO PROFILNYA
      $filepath = $mahasiswa->foto_profil;

      // MEMBUAT NAMA FILE DENGAN EXTENSI PNG 
      $filename = 'image' . DIRECTORY_SEPARATOR . str_random(40) . '.png';
      // UPLOAD FOTO
      file_put_contents($filename,base64_decode($image));
      // UPDATE FOTO
      $user = User::where('email',$request->user)->update(["foto_profil" => $filename]);
      // JIKA QUERY BERHASIL DI EKSKUSI
      if ($user == TRUE) {
        try {// hapus foto lama
          File::delete($filepath);
        } 
        catch (FileNotFoundException $e) {
          // File sudah dihapus/tidak ada
        }

        $response["value"] = 1;// value = 1
        $response["message"] = "Berhasil Mengubah Foto"; //  berhasil
      }
      else{
        $response["value"] = 0;// value = 1
        $response["message"] = "Gagal Mengubah Foto"; // GAGAL
      }

      return  json_encode($response);// data yang dikembalikan berupa json
    }
    //UPDATE PROFIL MAHASISWA
}//END CLASS
