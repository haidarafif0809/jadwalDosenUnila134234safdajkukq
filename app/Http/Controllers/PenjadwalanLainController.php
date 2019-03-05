<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Penjadwalan; 
use App\User; 
use App\User_otoritas; 
use App\Jadwal_dosen; 
use App\Master_ruangan;
use App\Master_block; 
use App\Master_mata_kuliah;
use App\SettingWaktu;
use App\ModulBlok;
use Session;
use Auth;
use Excel;


class PenjadwalanLainController extends Controller
{
      //PROSES KE HALAMAN TAMBAH PENJADWALANA CSL 
    public function create_csl()
    { 
        //MENAMPILKAN USER YANG OTORITAS NYA DOSEN
        $users = DB::table('users')
        ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
        ->where('role_user.role_id',2)
        ->pluck('name','id'); 

        //MENAMPILKAN KELOMPOK YANG JENIS CSL 
        $kelompoks = DB::table('kelompok_mahasiswas')
        ->where('jenis_kelompok','CSL')
        ->pluck('nama_kelompok_mahasiswa','id'); 


        //MENAMPILKAN BLOCK SESUAI DENGAN YANG LOGIN(APABILA PJ DOSEN YANG LOGIN MAKA BLOCK NYA YANG MUNCUL USER PJ DOSEN YANG ADA DI BLOK DAN APABILA ADMIN MAKA MUNCUL SEMUA BLOCK)
        $pj_dosen = Auth::user()->id;
        $data_block = DB::table('master_blocks')
        ->leftJoin('user_pj_dosens', 'master_blocks.id', '=', 'user_pj_dosens.id_master_block')
        ->where('user_pj_dosens.id_pj_dosen',$pj_dosen)
        ->pluck('master_blocks.nama_block','master_blocks.id'); 

        return view('penjadwalans_csl.create',['users' => $users,'data_block' => $data_block,'kelompoks' => $kelompoks]);
    }

    //PROSES KE HALAMAN TAMBAH PENJADWALANA TUTORIAL 
    public function create_tutorial()
    { 
        //MENAMPILKAN USER YANG OTORITAS NYA DOSEN
        $users = DB::table('users')
        ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
        ->where('role_user.role_id',2)
        ->pluck('name','id'); 

        //MENAMPILKAN KELOMPOK YANG JENIS TUTORIAL 
        $kelompoks = DB::table('kelompok_mahasiswas')
        ->where('jenis_kelompok','TUTORIAL')
        ->pluck('nama_kelompok_mahasiswa','id'); 


        //MENAMPILKAN BLOCK SESUAI DENGAN YANG LOGIN(APABILA PJ DOSEN YANG LOGIN MAKA BLOCK NYA YANG MUNCUL USER PJ DOSEN YANG ADA DI BLOK DAN APABILA ADMIN MAKA MUNCUL SEMUA BLOCK)
        $pj_dosen = Auth::user()->id;
        $data_block = DB::table('master_blocks')
        ->leftJoin('user_pj_dosens', 'master_blocks.id', '=', 'user_pj_dosens.id_master_block')
        ->where('user_pj_dosens.id_pj_dosen',$pj_dosen)
        ->pluck('master_blocks.nama_block','master_blocks.id'); 

        return view('penjadwalans_tutorial.create',['users' => $users,'data_block' => $data_block,'kelompoks' => $kelompoks]);
    }

    public function store(Request $request){

        $this->validate($request, [
            'id_block'    => 'required|exists:master_blocks,id',
            'id_ruangan'    => 'required|exists:master_ruangans,id',
            'id_user'    => 'required|exists:users,id',
            'id_materi'    => 'required',
            'id_kelompok' => 'required'
        ]);
        if($request->data_waktu == "" AND $request->tanggal == "" AND $request->data_waktu_2 == "" AND $request->tanggal_2 == ""){
            Session::flash("flash_notification", [
                "level"=>"danger",
                "message"=>"Tanggal Pertemuan dan Waktu Pertemuan Harus di Isi"
            ]);

            return redirect()->back()->withInput();
        }

        //jenis kelompok 
        $jenis_kelompok = $request->jenis_kelompok;
        //jenis kelompok 

        // jika pertemuan satu kosong dan pertemuan kedua di isi
        if($request->data_waktu == "" AND $request->tanggal == "" AND $request->data_waktu_2 != "" AND $request->tanggal_2 != ""){
            //MEMISAHKAN WAKTU MULAI DAN SELESAIA
            $data_setting_waktu_2 = explode("-",$request->data_waktu_2);

            //MENGECEK PENJADWLAN
            $data_penjadwalan_2 = Penjadwalan::statusRuanganCsl($request,$data_setting_waktu_2); 


        // cek apakah ruangan sudah dipakai
            if ($data_penjadwalan_2->count() == 0) { 

            //MENGECEK DOSEN DI JADWALAN YANG SAMA
                $dosen_punya_jadwal = array();
                foreach ($request->id_user as $user_dosen) {
                   $tanggal = $request->tanggal_2;  
                   $jadwal_dosen_2 = Jadwal_dosen::statusDosen($tanggal,$user_dosen,$data_setting_waktu_2); 
                   $data_jadwal_dosen_2 = $jadwal_dosen_2->first(); 

                   if ($jadwal_dosen_2->count() > 0) {
                      array_push($dosen_punya_jadwal,
                       ['id_jadwal'=>$data_jadwal_dosen_2->id_jadwal,
                       'id_dosen'=>$data_jadwal_dosen_2->id_dosen]);
                  }
              } 
            //APABILA JADWAL NYA SAMA MAKA MUNCUL PERINGATAN
              if (count($dosen_punya_jadwal) > 0 ) { 
                $message = 'Tidak Bisa Menambahkan Dosen Berikut Karena Sudah Memiliki Jadwal :<ul>'; 

                foreach ($dosen_punya_jadwal as $dosen_punya_jadwals) {  
                    $nama_dosen = User::find($dosen_punya_jadwals['id_dosen']);
                    $data_penjadwalans = Penjadwalan::find($dosen_punya_jadwals['id_jadwal']); 

                    $message .= "<li><b>$nama_dosen->name</b> Memilik Jadwal Di Block <b>".$data_penjadwalans->block->nama_block."</b></li>";

                }
                $message .= '</ul>';

                Session::flash("flash_notification", [
                    "level"=>"danger",
                    "message"=>"$message"
                ]); 
                return redirect()->back()->withInput();
            }
        }
        else{
            //APABILA RUANGAN SUDAH DI PAKAI DI WAKTU YANG BERSAMAAN MAKA MUNCUL ALERT DI BAWAH
            $data_ruangan =  Master_ruangan::find($request->id_ruangan);
            $data_block = Master_block::find($request->id_block);

            Session::flash("flash_notification", [
                "level"=>"danger",
                "message"=>"Ruangan $data_ruangan->nama_ruangan Sudah Di Pakai Block $data_block->nama_block"
            ]);

            return redirect()->back()->withInput();
        } 

    }
    elseif($request->data_waktu_2 == "" AND $request->tanggal_2 == "" AND $request->data_waktu != "" AND $request->tanggal != ""){
            //MEMISAHKAN WAKTU MULAI DAN SELESAIA
        $data_setting_waktu = explode("-",$request->data_waktu);

        //cek status ruangan
        $data_penjadwalan = Penjadwalan::statusRuanganCsl($request,$data_setting_waktu);  

        //cek apakah ruangan yang di pilih sudah terpakai
        if ($data_penjadwalan->count() == 0) { 

            //cek dosen apakah sudah mempunyai jadwal
            $dosen_punya_jadwal = array();
            foreach ($request->id_user as $user_dosen) {
               $tanggal = $request->tanggal;
               $jadwal_dosen = Jadwal_dosen::statusDosen($tanggal,$user_dosen,$data_setting_waktu); 
               $data_jadwal_dosen = $jadwal_dosen->first(); 

               if ($jadwal_dosen->count() > 0) {
                  array_push($dosen_punya_jadwal,
                   ['id_jadwal'=>$data_jadwal_dosen->id_jadwal,
                   'id_dosen'=>$data_jadwal_dosen->id_dosen]);
              }
          } 
            //jika dosen sudah mempunyai jadwal maka tampilkan pesan bahwa dosen sudah punya jadwal
          if (count($dosen_punya_jadwal) > 0 ) { 
            $message = 'Tidak Bisa Menambahkan Dosen Berikut Karena Sudah Memiliki Jadwal :<ul>'; 

            foreach ($dosen_punya_jadwal as $dosen_punya_jadwals) {  
                $nama_dosen = User::find($dosen_punya_jadwals['id_dosen']);
                $data_penjadwalans = Penjadwalan::find($dosen_punya_jadwals['id_jadwal']); 

                $message .= "<li><b>$nama_dosen->name</b> Memilik Jadwal Di  Block <b>".$data_penjadwalans->block->nama_block."</b></li>";

            }
            $message .= '</ul>';

            Session::flash("flash_notification", [
                "level"=>"danger",
                "message"=>"$message"
            ]); 
            return redirect()->back()->withInput();
        }

    }
    else{
            //APABILA RUANGAN SUDAH DI PAKAI DI WAKTU YANG BERSAMAAN MAKA MUNCUL ALERT DI BAWAH
        $data_ruangan =  Master_ruangan::find($request->id_ruangan);
        $data_block = Master_block::find($request->id_block);

        Session::flash("flash_notification", [
            "level"=>"danger",
            "message"=>"Ruangan $data_ruangan->nama_ruangan Sudah Di Pakai Block $data_block->nama_block"
        ]);

        return redirect()->back()->withInput();
    } 

}
else{
            //MEMISAHKAN WAKTU MULAI DAN SELESAIA
    $data_setting_waktu = explode("-",$request->data_waktu);
    $data_setting_waktu_2 = explode("-",$request->data_waktu_2);

            //MENGECEK PENJADWLAN
    $data_penjadwalan = Penjadwalan::statusRuanganCsl($request,$data_setting_waktu);  

    $data_penjadwalan_2 = Penjadwalan::statusRuanganCsl($request,$data_setting_waktu_2); 

                    //APABILA $data_penjadwalan == 0 maka ngecek dosen
    if ($data_penjadwalan->count() == 0 OR $data_penjadwalan_2->count() == 0) { 

            //MENGECEK DOSEN DI JADWALAN YANG SAMA
        $dosen_punya_jadwal = array();
        foreach ($request->id_user as $user_dosen) {
           $tanggal = $request->tanggal;
           $jadwal_dosen = Jadwal_dosen::statusDosen($tanggal,$user_dosen,$data_setting_waktu); 
           $tanggal = $request->tanggal_2;
           $jadwal_dosen_2 = Jadwal_dosen::statusDosen($tanggal,$user_dosen,$data_setting_waktu_2); 

           $data_jadwal_dosen = $jadwal_dosen->first(); 

           $data_jadwal_dosen_2 = $jadwal_dosen_2->first(); 

           if ($jadwal_dosen->count() > 0) {
            array_push($dosen_punya_jadwal,
               ['id_jadwal'=>$data_jadwal_dosen->id_jadwal,
               'id_dosen'=>$data_jadwal_dosen->id_dosen]);
        }

        if ($jadwal_dosen_2->count() > 0) {
          array_push($dosen_punya_jadwal,
           ['id_jadwal'=>$data_jadwal_dosen_2->id_jadwal,
           'id_dosen'=>$data_jadwal_dosen_2->id_dosen]);
      }
  } 
            //APABILA JADWAL NYA SAMA MAKA MUNCUL PERINGATAN
  if (count($dosen_punya_jadwal) > 0 ) { 
    $message = 'Tidak Bisa Menambahkan Dosen Berikut Karena Sudah Memiliki Jadwal :<ul>'; 

    foreach ($dosen_punya_jadwal as $dosen_punya_jadwals) {  
        $nama_dosen = User::find($dosen_punya_jadwals['id_dosen']);
        $data_penjadwalans = Penjadwalan::find($dosen_punya_jadwals['id_jadwal']); 

        $message .= "<li><b>$nama_dosen->name</b> Memilik Jadwal Di  Block <b>".$data_penjadwalans->block->nama_block."</b></li>";

    }
    $message .= '</ul>';

    Session::flash("flash_notification", [
        "level"=>"danger",
        "message"=>"$message"
    ]); 
    return redirect()->back()->withInput();
}
}
else{
            //APABILA RUANGAN SUDAH DI PAKAI DI WAKTU YANG BERSAMAAN MAKA MUNCUL ALERT DI BAWAH
    $data_ruangan =  Master_ruangan::find($request->id_ruangan);
    $data_block = Master_block::find($request->id_block);

    Session::flash("flash_notification", [
        "level"=>"danger",
        "message"=>"Ruangan $data_ruangan->nama_ruangan Sudah Di Pakai Block $data_block->nama_block"
    ]);

    return redirect()->back()->withInput();
} 

}

$kelompok_punya_jadwal = 0;
$message = '';
if ($request->tanggal != "") {
            //cek apakah kelompok mahasiswa sudah memiliki jadwal di pertemuan ke satu 
   $data_setting_waktu = explode("-",$request->data_waktu);
   $kelompok_pertemuan_1 = Penjadwalan::statusKelompok($request->id_kelompok,$request->tanggal,$data_setting_waktu);
   if ($kelompok_pertemuan_1->count() > 0) {
    $kelompok_punya_jadwal += 1;
    $nama_kelompok_mahasiswa = $kelompok_pertemuan_1->first()->kelompok->nama_kelompok_mahasiswa;
    $nama_ruangan = $kelompok_pertemuan_1->first()->ruangan->nama_ruangan ;
    $message .= "<li>Untuk Pertemuan Ke Satu Kelompok $nama_kelompok_mahasiswa Sudah Memiliki Jadwal di Ruangan $nama_ruangan </li> ";
}
}   
if ($request->tanggal_2 != "") {
            //cek apakah kelompok mahasiswa sudah memiliki jadwal di pertemuan  kedua
   $data_setting_waktu_2 = explode("-",$request->data_waktu_2);
   $kelompok_pertemuan_2 = Penjadwalan::statusKelompok($request->id_kelompok,$request->tanggal_2,$data_setting_waktu_2);
   if ($kelompok_pertemuan_2->count() > 0) {
    $kelompok_punya_jadwal += 1;
    $nama_kelompok_mahasiswa = $kelompok_pertemuan_2->first()->kelompok->nama_kelompok_mahasiswa;
    $nama_ruangan = $kelompok_pertemuan_2->first()->ruangan->nama_ruangan ;
    $message .= "<li>Untuk Pertemuan Ke Dua Kelompok $nama_kelompok_mahasiswa Sudah Memiliki Jadwal di Ruangan $nama_ruangan </li> ";
}
}

if ($kelompok_punya_jadwal > 0) {
    Session::flash("flash_notification", [
        "level"=>"danger",
        "message"=> $message    
    ]);

    return redirect()->back()->withInput();
}








        //JIKA WAKTU DAN TANGGAL  PERTEMUAN SATU KOSONG  YANG MASUKAN PERTEMUAN DUA

if($request->data_waktu_2 != "" AND $request->tanggal_2 != ""){

    $penjadwalan_2 = Penjadwalan::create([ 
        'tanggal' =>$request->tanggal_2,
        'waktu_mulai'=>$data_setting_waktu_2[0],
        'waktu_selesai'=>$data_setting_waktu_2[1],
        'id_block'=>$request->id_block,
        'id_materi'=>$request->id_materi,
        'tipe_jadwal'=>$jenis_kelompok,
        'id_mata_kuliah'=>"-",
        'id_ruangan'=>$request->id_ruangan,
        'id_kelompok'=>$request->id_kelompok]);
}
       //JIKA WAKTU DAN TANGGAL  PERTEMUAN DUA KOSONG  YANG MASUKAN PERTEMUAN SATU
if($request->data_waktu != "" AND $request->tanggal != ""){

    $penjadwalan = Penjadwalan::create([ 
        'tanggal' =>$request->tanggal,
        'waktu_mulai'=>$data_setting_waktu[0],
        'waktu_selesai'=>$data_setting_waktu[1],
        'id_block'=>$request->id_block,
        'id_materi'=>$request->id_materi,
        'tipe_jadwal'=>$jenis_kelompok,
        'id_mata_kuliah'=>"-",
        'id_ruangan'=>$request->id_ruangan,
        'id_kelompok'=>$request->id_kelompok]);

}


        //UNTUK MEMBUAT JADWAL DOSEN YANG BERKAIT SAMA PENJADWALAN
foreach ($request->id_user as $user_dosen) {

//JIKA WAKTU DAN TANGGAL  PERTEMUAN DUA TIDAK KOSONG maka masukkan jadwalnya 
    if($request->data_waktu_2 != "" AND $request->tanggal_2 != ""){

       $jadwal_dosen_2 = Jadwal_dosen::create([ 
        'id_jadwal' =>$penjadwalan_2->id,
        'id_dosen'=>$user_dosen,
        'id_block'=>$request->id_block,
        'id_mata_kuliah'=>"-",
        'id_ruangan'=>$request->id_ruangan,
        'tanggal' =>$request->tanggal_2,
        'waktu_mulai'=>$data_setting_waktu_2[0],
        'waktu_selesai'=>$data_setting_waktu_2[1],
        'tipe_jadwal'=>$jenis_kelompok,

    ]);
   }
       //JIKA WAKTU DAN TANGGAL  PERTEMUAN SATU TIDAK KOSONG maka masukkan jadwalnya 
   if($request->data_waktu != "" AND $request->tanggal != ""){

    $jadwal_dosen = Jadwal_dosen::create([ 
        'id_jadwal' =>$penjadwalan->id,
        'id_dosen'=>$user_dosen,
        'id_block'=>$request->id_block,
        'id_mata_kuliah'=>"-",
        'id_ruangan'=>$request->id_ruangan,
        'tanggal' =>$request->tanggal,
        'waktu_mulai'=>$data_setting_waktu[0],
        'waktu_selesai'=>$data_setting_waktu[1],
        'tipe_jadwal'=>$jenis_kelompok,

    ]);
}

    } // end foreach jadwal dosen
        //ALERT JIKA BERHASIL
    Session::flash("flash_notification", [
        "level"=>"success",
        "message"=>"Berhasil Menambah Penjadwalan"
    ]);

        //APABILA TAMBAH DI BLOCK->MODUL->JADWAL
    if (isset($request->asal_input)) {
            # code...
        return redirect()->back();
        }//APABILA TAMBAH DI PENJADWALAN->TAMBAH PENJADWALAN
        else {
            return redirect()->route('penjadwalans.index'); 
        }

    }

    public function proses_ubah_dosen_csl_tutorial(Request $request, $id) 
    { 
        //NGAMBIL DATA DARI FORM EDIT 
        $this->validate($request, [ 
            'tanggal'   => 'required', 
            'data_waktu'     => 'required',  
            'id_block'    => 'required|exists:master_blocks,id', 
            'id_ruangan'    => 'required|exists:master_ruangans,id', 
            'id_user'    => 'required|exists:users,id', 
            'id_materi'    => 'required', 
            'id_kelompok' => 'required' 
        ]);   


         //MEMISAHKAN WAKTU MULAI DAN SELESAI 
        $data_setting_waktu = explode("-",$request->data_waktu); 
        $jenis_kelompok = $request->jenis_kelompok; 

            //MENGECEK DATA YANG SAMA APA TIDAK 
        $penjadwalans = Penjadwalan::find($id);  

        $data_penjadwalan = JadwalRuangan::statusRuanganEditCsl($request,$data_setting_waktu,$id);  
            //APABILA $data_penjadwalan == 0 maka ngecek dosen 
        if ($data_penjadwalan->count() == 0) { 
            $dosen_punya_jadwal = array(); 
            foreach ($request->id_user as $user_dosen) { 
               $jadwal_dosen = Jadwal_dosen::statusDosenEdit($request,$user_dosen,$data_setting_waktu,$id);  
               $data_jadwal_dosen = $jadwal_dosen->first();  

               if ($jadwal_dosen->count() > 0) { 
                array_push($dosen_punya_jadwal, ['id_jadwal'=>$data_jadwal_dosen->id_jadwal,'id_dosen'=>$data_jadwal_dosen->id_dosen]); 
            } 
        }  
            //APABILA JADWAL NYA SAMA MAKA MUNCUL PERINGATAN 
        if (count($dosen_punya_jadwal) > 0 ) {  
            $message = 'Tidak Bisa Menambahkan Dosen Berikut Karena Sudah Memiliki Jadwal :<ul>';  
            foreach ($dosen_punya_jadwal as $dosen_punya_jadwals) {   
                $nama_dosen = User::find($dosen_punya_jadwals['id_dosen']); 
                $data_penjadwalans = Penjadwalan::find($dosen_punya_jadwals['id_jadwal']);  

                $message .= "<li><b>$nama_dosen->name</b> Memilik Jadwal Di Ruangan <b>".$data_penjadwalans->ruangan->nama_ruangan."</b> Block <b>".$data_penjadwalans->block->nama_block."</b> </b> </li>";    
            } 
            $message .= '</ul>'; 

            Session::flash("flash_notification", [ 
                "level"=>"danger", 
                "message"=>"$message" 
            ]);  
            return redirect()->back()->withInput(); 
        } 
    } 
    else{  
            //APABILA RUANGAN SUDAH DI PAKAI MAKA MUNCUL PERINGATAN  
        $data_ruangan =  Master_ruangan::find($request->id_ruangan); 
        $data_block = Master_block::find($data_penjadwalan->first()->id_block); 

        Session::flash("flash_notification", [ 
            "level"=>"danger", 
            "message"=>"Ruangan $data_ruangan->nama_ruangan Sudah Di Pakai Block $data_block->nama_block " 
        ]); 
        return redirect()->back()->withInput(); 
    } 

             //JIKA PENJADWALAN TIDAK ADA YANG SAMA MAKA PROSES TAMBAH PENJADWALAN BERHASIL  
    $penjadwalan = Penjadwalan::find($id)->update(["status_jadwal" => 3]); 
    $jadwal_dosen = Jadwal_dosen::where("id_jadwal",$id)->update(["status_jadwal" => 3]); 


        //JIKA PENJADWALAN TIDAK ADA YANG SAMA MAKA PROSES TAMBAH PENJADWALAN BERHASIL 
    $penjadwalan = Penjadwalan::create([  
        'tanggal' =>$request->tanggal, 
        'waktu_mulai'=>$data_setting_waktu[0] , 
        'waktu_selesai'=>$data_setting_waktu[1] , 
        'id_materi'=>$request->id_materi, 
        'id_block'=>$request->id_block, 
        'id_ruangan'=>$request->id_ruangan, 
        'id_kelompok'=>$request->id_kelompok, 
        'tipe_jadwal'=>$jenis_kelompok,]); 


            //MEMBUAT BARU JADWAL DOSEN 
    foreach ($request->id_user as $user_dosen) { 
                # code... 
        $jadwal_dosen = Jadwal_dosen::create([  
            'id_jadwal' =>$penjadwalan->id, 
            'id_dosen'=>$user_dosen, 
            'id_block'=>$request->id_block, 
            'id_ruangan'=>$request->id_ruangan, 
            'tanggal' =>$request->tanggal, 
            'waktu_mulai'=>$data_setting_waktu[0], 
            'waktu_selesai'=>$data_setting_waktu[1], 
            'tipe_jadwal'=>$jenis_kelompok, 

        ]); 

    } 

    Session::flash("flash_notification", [ 
        "level"=>"success", 
        "message"=>"Penjadwalan Berhasil Di Ubah" 
    ]); 
    return redirect()->route('penjadwalans.index'); 


} 

        //PROSES UPDATE PENJADWALAN 
public function update(Request $request, $id)
{
        //NGAMBIL DATA DARI FORM EDIT
    $this->validate($request, [
        'tanggal'   => 'required',
        'data_waktu'     => 'required', 
        'id_block'    => 'required|exists:master_blocks,id',
        'id_ruangan'    => 'required|exists:master_ruangans,id',
        'id_user'    => 'required|exists:users,id',
        'id_materi'    => 'required',
        'id_kelompok' => 'required'
    ]);  


         //MEMISAHKAN WAKTU MULAI DAN SELESAI
    $data_setting_waktu = explode("-",$request->data_waktu);
    $jenis_kelompok = $request->jenis_kelompok;

            //MENGECEK DATA YANG SAMA APA TIDAK
    $penjadwalans = Penjadwalan::find($id); 
    $data_penjadwalan = Penjadwalan::statusRuanganEditCsl($request,$data_setting_waktu,$id); 
            //APABILA $data_penjadwalan == 0 maka ngecek dosen
    if ($data_penjadwalan->count() == 0) {
        $dosen_punya_jadwal = array();
        foreach ($request->id_user as $user_dosen) {
           $tanggal = $request->tanggal;
           $jadwal_dosen = Jadwal_dosen::statusDosenEdit($tanggal,$user_dosen,$data_setting_waktu,$id); 
           $data_jadwal_dosen = $jadwal_dosen->first(); 

           if ($jadwal_dosen->count() > 0) {
            array_push($dosen_punya_jadwal, ['id_jadwal'=>$data_jadwal_dosen->id_jadwal,'id_dosen'=>$data_jadwal_dosen->id_dosen]);
        }
    } 
            //APABILA JADWAL NYA SAMA MAKA MUNCUL PERINGATAN
    if (count($dosen_punya_jadwal) > 0 ) { 
        $message = 'Tidak Bisa Menambahkan Dosen Berikut Karena Sudah Memiliki Jadwal :<ul>'; 
        foreach ($dosen_punya_jadwal as $dosen_punya_jadwals) {  
            $nama_dosen = User::find($dosen_punya_jadwals['id_dosen']);
            $data_penjadwalans = Penjadwalan::find($dosen_punya_jadwals['id_jadwal']); 

            $message .= "<li><b>$nama_dosen->name</b> Memilik Jadwal Di Ruangan <b>".$data_penjadwalans->ruangan->nama_ruangan."</b> Block <b>".$data_penjadwalans->block->nama_block."</b> </b> </li>";   
        }
        $message .= '</ul>';

        Session::flash("flash_notification", [
            "level"=>"danger",
            "message"=>"$message"
        ]); 
        return redirect()->back()->withInput();
    }
}
else{ 
            //APABILA RUANGAN SUDAH DI PAKAI MAKA MUNCUL PERINGATAN 
    $data_ruangan =  Master_ruangan::find($request->id_ruangan);
    $data_block = Master_block::find($data_penjadwalan->first()->id_block);

    Session::flash("flash_notification", [
        "level"=>"danger",
        "message"=>"Ruangan $data_ruangan->nama_ruangan Sudah Di Pakai Block $data_block->nama_block "
    ]);
    return redirect()->back()->withInput();
} 

        //JIKA PENJADWALAN TIDAK ADA YANG SAMA MAKA PROSES TAMBAH PENJADWALAN BERHASIL
$penjadwalan = Penjadwalan::where('id', $id)->update([ 
    'tanggal' =>$request->tanggal,
    'waktu_mulai'=>$data_setting_waktu[0] ,
    'waktu_selesai'=>$data_setting_waktu[1] ,
    'id_materi'=>$request->id_materi,
    'id_block'=>$request->id_block,
    'id_ruangan'=>$request->id_ruangan,
    'id_kelompok'=>$request->id_kelompok]);


            //MENGHAPUS JADWAL DOSEN
Jadwal_dosen::where('id_jadwal', $id)->delete(); 
            //MEMBUAT BARU JADWAL DOSEN
foreach ($request->id_user as $user_dosen) {
                # code...
    $jadwal_dosen = Jadwal_dosen::create([ 
        'id_jadwal' =>$id,
        'id_dosen'=>$user_dosen,
        'id_block'=>$request->id_block,
        'id_ruangan'=>$request->id_ruangan,
        'tanggal' =>$request->tanggal,
        'waktu_mulai'=>$data_setting_waktu[0],
        'waktu_selesai'=>$data_setting_waktu[1],
        'tipe_jadwal'=>$jenis_kelompok,

    ]);

}

Session::flash("flash_notification", [
    "level"=>"success",
    "message"=>"Penjadwalan Berhasil Di Ubah"
]);
return redirect()->route('penjadwalans.index');
}

}
