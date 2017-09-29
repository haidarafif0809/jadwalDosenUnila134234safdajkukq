<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use App\Penjadwalan;
use App\Jadwal_dosen;
use App\Presensi;
use App\Master_block;
use App\Master_mata_kuliah;
use Excel;
use Illuminate\Support\Facades\DB;



class LaporanPresensiDosenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          //MENAMPILKAN USER YANG OTORITAS NYA DOSEN
          $dosen = DB::table('users')
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id',2)
            ->pluck('name','id');

            // tambahakan value semua dosen
           $dosen->prepend('Semua Dosen', 'semua'); 

        // SELECT MASTER BLOCK, UNTUK DIAMBIL NAMA DAN ID BLOCK NYA
        $master_blocks = Master_block::select('nama_block','id')->get();
        
        // RETURN VIEW lap_presensi_dosen.index, DAN KITA PASSING $master_blocks
        return view("lap_presensi_dosen.index",[
            'master_blocks' => $master_blocks,
            'dosen' => $dosen
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */ // proses lap rekap presensi dosen
    public function store(Request $request)
    {
      // jika dosen == semua AND tipe jadwal == SEMUA
      if ($request->dosen == 'semua' AND $request->tipe_jadwal == 'SEMUA') {
                // SELECT JADWAL DOSEN WHERE ID BLOCK = $request->id_block dan kita with dosen

        $jadwal_dosens = Jadwal_dosen::select('penjadwalans.tipe_jadwal AS tipe_jadwal','users.name AS nama_dosen','jadwal_dosens.id_dosen AS id_dosen')// SELECT NAMA DOSEN, ID DOSEN
                        ->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')// LEFT JOIN PENJADWALAN ON ID JADWAL = ID
                        ->leftJoin('users','jadwal_dosens.id_dosen','=','users.id')// LEFT JOIN USER ON ID DOSEN = ID
                        ->where('jadwal_dosens.id_block',$request->id_block)// WHERE ID BLOCK
                        ->groupBy('jadwal_dosens.id_dosen')->get();// GROUP BY ID DOSEN

        }else if ($request->dosen == 'semua' AND $request->tipe_jadwal != 'SEMUA') {
                // SELECT JADWAL DOSEN WHERE ID BLOCK = $request->id_block dan kita with dosen

          $jadwal_dosens = Jadwal_dosen::select('penjadwalans.tipe_jadwal AS tipe_jadwal','users.name AS nama_dosen','jadwal_dosens.id_dosen AS id_dosen')// SELECT NAMA DOSEN, ID DOSEN
                        ->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')// LEFT JOIN PENJADWALAN ON ID JADWAL = ID
                        ->leftJoin('users','jadwal_dosens.id_dosen','=','users.id')// LEFT JOIN USER ON ID DOSEN = ID
                        ->where('jadwal_dosens.id_block',$request->id_block)// WHERE ID BLOCK
                        ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)// AND TIPE JADWAL 
                        ->groupBy('jadwal_dosens.id_dosen')->get();// GROUP BY ID DOSEN

        }else if ($request->dosen != 'semua' AND $request->tipe_jadwal == 'SEMUA') {
                // SELECT JADWAL DOSEN WHERE ID BLOCK = $request->id_block dan kita with dosen

          $jadwal_dosens = Jadwal_dosen::select('penjadwalans.tipe_jadwal AS tipe_jadwal','users.name AS nama_dosen','jadwal_dosens.id_dosen AS id_dosen')// SELECT NAMA DOSEN, ID DOSEN
                        ->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')// LEFT JOIN PENJADWALAN ON ID JADWAL = ID
                        ->leftJoin('users','jadwal_dosens.id_dosen','=','users.id')// LEFT JOIN USER ON ID DOSEN = ID
                        ->where('jadwal_dosens.id_block',$request->id_block)// WHERE ID BLOCK
                        ->where('jadwal_dosens.id_dosen',$request->dosen)// AND ID DOSEN
                        ->groupBy('jadwal_dosens.id_dosen')->get();// GROUP BY ID DOSEN
        }else{

        $jadwal_dosens = Jadwal_dosen::select('penjadwalans.tipe_jadwal AS tipe_jadwal','users.name AS nama_dosen','jadwal_dosens.id_dosen AS id_dosen')// SELECT NAMA DOSEN, ID DOSEN
                        ->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')// LEFT JOIN PENJADWALAN ON ID JADWAL = ID
                        ->leftJoin('users','jadwal_dosens.id_dosen','=','users.id')// LEFT JOIN USER ON ID DOSEN = ID
                        ->where('jadwal_dosens.id_block',$request->id_block)// WHERE ID BLOCK
                        ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)// AND TIPE JADWAL 
                        ->where('jadwal_dosens.id_dosen',$request->dosen)// AND ID DOSEN
                        ->groupBy('jadwal_dosens.id_dosen')->get(); // GROUP BY ID DOSEN
        }

            return Datatables::of($jadwal_dosens)
                        // edit kolom nama dosen
                        ->editColumn('nama_dosen', function ($jadwal_dosen) use ($request) {
                        // ambil nama dosen
                        return $jadwal_dosen['nama_dosen'];
                        })
                                         // EDIT KOLOM JUMLAH JADWAL
                        ->editColumn('jumlah_jadwal', function ($jadwal_dosen) use ($request) {
                            // $jadwal dosen kita tambahkan LEFT JOIN PENJADWALAN where id dosen , id block, DAN TIPE JADWAL kemudian kita count untuk mendapatakan jumlah jadwal nya

                              if ($request->tipe_jadwal == 'SEMUA') {

                                  return $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                         ->where('jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                         ->where('jadwal_dosens.id_block',$request->id_block)
                                         ->count();
                              }else{

                                  return $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                         ->where('jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                         ->where('jadwal_dosens.id_block',$request->id_block)
                                         ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)
                                         ->count();
                              }


                        }) 
                               // edit kolom jumlah_hadir
                        ->editColumn('jumlah_hadir', function ($jadwal_dosen) use ($request) {
                            // SELECT COUNT PRESENSI DOSEN ,  WHERE ID DOSEN DAN ID BLOCK

                           if ($request->tipe_jadwal == 'SEMUA') {
                                  
                                  return Presensi::leftJoin('penjadwalans','presensi.id_jadwal','=','penjadwalans.id')
                                          ->where('presensi.id_user',$jadwal_dosen['id_dosen'])
                                          ->where('presensi.id_block',$request->id_block)
                                          ->count();
                            }else{
                                  
                                  return Presensi::leftJoin('penjadwalans','presensi.id_jadwal','=','penjadwalans.id')
                                          ->where('presensi.id_user',$jadwal_dosen['id_dosen'])
                                          ->where('presensi.id_block',$request->id_block)
                                          ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)
                                          ->count();       
                             }

                        })
                        // EDIT KOLOM JUMLAH JADWAL TERLAKSANA
                        ->editColumn('terlaksana', function ($jadwal_dosen) use ($request) {
                            // $jadwal dosen kita tambahkan LEFT JOIN PENJADWALAN where id dosen, status jadwal = 1(terlakasana) dan id block DAN TIPE JADWA, kemudian kita count untuk mendapatakan jumlah jadwal nya
                            if ($request->tipe_jadwal == 'SEMUA') {

                              return $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                     ->where('jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                     ->where('jadwal_dosens.status_jadwal',1)
                                     ->where('jadwal_dosens.id_block',$request->id_block)
                                     ->count();
                             }else{

                              return $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                     ->where('jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                     ->where('jadwal_dosens.status_jadwal',1)
                                     ->where('jadwal_dosens.id_block',$request->id_block)
                                     ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)
                                     ->count();
                              }
                        
                        })
                        // EDIT KOLOM JUMLAH JADWAL BELUM TERLAKSANA
                        ->editColumn('belum_terlaksana', function ($jadwal_dosen) use ($request) {
                            // $jadwal dosen kita tambahkan LEFT JOIN PENJADWALAN where id dosen, status jadwal = 0(belum_terlakasana) dan id block DAN TIPE JADWA, kemudian kita count untuk mendapatakan jumlah jadwal nya

                               if($request->tipe_jadwal == 'SEMUA') {

                                return $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                                  ->where('jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                                  ->where('jadwal_dosens.status_jadwal',0)
                                                  ->where('jadwal_dosens.id_block',$request->id_block)
                                                  ->count();
                              }else{

                              return $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                                  ->where('jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                                  ->where('jadwal_dosens.status_jadwal',0)
                                                  ->where('jadwal_dosens.id_block',$request->id_block)
                                                  ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)
                                                  ->count();
                              }
                        
                        })
                        // EDIT KOLOM JUMLAH JADWAL BATAL TERLAKSANA
                        ->editColumn('batal', function ($jadwal_dosen) use ($request) {
                            // $jadwal dosen kita tambahkan LEFT JOIN PENJADWALAN where id dosen, status jadwal = 2(BATAL) dan id block DAN TIPE JADWA, kemudian kita count untuk mendapatakan jumlah jadwal nya
                             if($request->tipe_jadwal == 'SEMUA') {

                              return $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                            ->where('jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                            ->where('jadwal_dosens.status_jadwal',2)
                                            ->where('jadwal_dosens.id_block',$request->id_block)
                                            ->count();
                            }else{

                              return $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                            ->where('jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                            ->where('jadwal_dosens.status_jadwal',2)
                                            ->where('jadwal_dosens.id_block',$request->id_block)
                                            ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)
                                            ->count();
                            }
                        })
                        // EDIT KOLOM JUMLAH JADWAL digantikan
                        ->editColumn('digantikan', function ($jadwal_dosen) use ($request) {
                            // $jadwal dosen kita tambahkan LEFT JOIN PENJADWALAN where id dosen, status jadwal = 3(digantikan) dan id block DAN TIPE JADWA, kemudian kita count untuk mendapatakan jumlah jadwal nya
                            if($request->tipe_jadwal == 'SEMUA') {

                              return $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                            ->where('jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                            ->where('jadwal_dosens.status_jadwal',3)
                                            ->where('jadwal_dosens.id_block',$request->id_block)
                                            ->count();
                            }else{

                              return $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                            ->where('jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                            ->where('jadwal_dosens.status_jadwal',3)
                                            ->where('jadwal_dosens.id_block',$request->id_block)
                                            ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)
                                            ->count();
                            }

                        })

                        ->editColumn('presentasi', function ($jadwal_dosen) use ($request) {

                           // SELECT COUNT PRESENSI DOSEN ,  WHERE ID DOSEN DAN ID BLOCK
                            if($request->tipe_jadwal == 'SEMUA') {

                           $jumlah_hadir = Presensi::leftJoin('penjadwalans','presensi.id_jadwal','=','penjadwalans.id')
                                          ->where('presensi.id_user',$jadwal_dosen['id_dosen'])
                                          ->where('presensi.id_block',$request->id_block)
                                          ->count();

                            // $jadwal dosen kita tambahkan where id dosen dan id block, kemudian kita count untuk mendapatakan jumlah jadwal nya
                           $jumlah_jadwal = $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                                        ->where('jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                                        ->where('jadwal_dosens.id_block',$request->id_block)
                                                        ->count();
                            }else{

                           $jumlah_hadir = Presensi::leftJoin('penjadwalans','presensi.id_jadwal','=','penjadwalans.id')
                                          ->where('presensi.id_user',$jadwal_dosen['id_dosen'])
                                          ->where('presensi.id_block',$request->id_block)
                                          ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)
                                          ->count();

                            // $jadwal dosen kita tambahkan where id dosen dan id block, kemudian kita count untuk mendapatakan jumlah jadwal nya
                           $jumlah_jadwal = $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                                        ->where('jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                                        ->where('jadwal_dosens.id_block',$request->id_block)
                                                        ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)
                                                        ->count();
                            }



                           //cara hitung persentasi kehadiran = jumlah hadir * 100 / jumlahjadwal
                           $presentasi = ($jumlah_hadir * 100) / $jumlah_jadwal;

                                // kita bulatkan 2 angka dibelakang koma hasil presentasinya dengan mengunakan fungsi count(,2)
                        return round($presentasi,2)." %";
                        })


            ->make(true);
    }// 


// export excel
        public function export_excel(Request $request)
    {
        $id_block = $request->id_block;

               if($request->dosen == 'semua' AND $request->tipe_jadwal == 'SEMUA') {
                        // SELECT JADWAL DOSEN WHERE ID BLOCK = $request->id_block dan kita with dosen

                $jadwal_dosens = Jadwal_dosen::select('penjadwalans.tipe_jadwal AS tipe_jadwal','users.name AS nama_dosen','jadwal_dosens.id_dosen AS id_dosen')// SELECT NAMA DOSEN, ID DOSEN
                                ->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')// LEFT JOIN PENJADWALAN ON ID JADWAL = ID
                                ->leftJoin('users','jadwal_dosens.id_dosen','=','users.id')// LEFT JOIN USER ON ID DOSEN = ID
                                ->where('jadwal_dosens.id_block',$request->id_block)// WHERE ID BLOCK
                                ->groupBy('jadwal_dosens.id_dosen')->get();// GROUP BY ID DOSEN

                }else if ($request->dosen == 'semua' AND $request->tipe_jadwal != 'SEMUA') {
                        // SELECT JADWAL DOSEN WHERE ID BLOCK = $request->id_block dan kita with dosen

                  $jadwal_dosens = Jadwal_dosen::select('penjadwalans.tipe_jadwal AS tipe_jadwal','users.name AS nama_dosen','jadwal_dosens.id_dosen AS id_dosen')// SELECT NAMA DOSEN, ID DOSEN
                                ->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')// LEFT JOIN PENJADWALAN ON ID JADWAL = ID
                                ->leftJoin('users','jadwal_dosens.id_dosen','=','users.id')// LEFT JOIN USER ON ID DOSEN = ID
                                ->where('jadwal_dosens.id_block',$request->id_block)// WHERE ID BLOCK
                                ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)// AND TIPE JADWAL 
                                ->groupBy('jadwal_dosens.id_dosen')->get();// GROUP BY ID DOSEN

                }else if ($request->dosen != 'semua' AND $request->tipe_jadwal == 'SEMUA') {
                        // SELECT JADWAL DOSEN WHERE ID BLOCK = $request->id_block dan kita with dosen

                  $jadwal_dosens = Jadwal_dosen::select('penjadwalans.tipe_jadwal AS tipe_jadwal','users.name AS nama_dosen','jadwal_dosens.id_dosen AS id_dosen')// SELECT NAMA DOSEN, ID DOSEN
                                ->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')// LEFT JOIN PENJADWALAN ON ID JADWAL = ID
                                ->leftJoin('users','jadwal_dosens.id_dosen','=','users.id')// LEFT JOIN USER ON ID DOSEN = ID
                                ->where('jadwal_dosens.id_block',$request->id_block)// WHERE ID BLOCK
                                ->where('jadwal_dosens.id_dosen',$request->dosen)// AND ID DOSEN
                                ->groupBy('jadwal_dosens.id_dosen')->get();// GROUP BY ID DOSEN
                }else{

                $jadwal_dosens = Jadwal_dosen::select('penjadwalans.tipe_jadwal AS tipe_jadwal','users.name AS nama_dosen','jadwal_dosens.id_dosen AS id_dosen')// SELECT NAMA DOSEN, ID DOSEN
                                ->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')// LEFT JOIN PENJADWALAN ON ID JADWAL = ID
                                ->leftJoin('users','jadwal_dosens.id_dosen','=','users.id')// LEFT JOIN USER ON ID DOSEN = ID
                                ->where('jadwal_dosens.id_block',$request->id_block)// WHERE ID BLOCK
                                ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)// AND TIPE JADWAL 
                                ->where('jadwal_dosens.id_dosen',$request->dosen)// AND ID DOSEN
                                ->groupBy('jadwal_dosens.id_dosen')->get(); // GROUP BY ID DOSEN
                }

     
         Excel::create("LAPORAN REKAP PRESENSI DOSEN", function($excel) use ($id_block, $jadwal_dosens,$request) {

            $excel->sheet("LAPORAN REKAP PRESENSI DOSEN", function ($sheet) use ($id_block, $jadwal_dosens,$request) {

                // judul kolom
                $row = 1;
                $sheet->row($row,[
              
                'Nama Dosen',
                'Jumlah Jadwal',                
                'Jumlah Hadir',
                'Terlaksana',
                'Belum Terlaksana',
                'Batal',
                'Digantikan',
                'Presentasi Kehadiran (%)'

                ]);


                foreach ($jadwal_dosens as $jadwal_dosen) {
                               // $jadwal dosen kita tambahkan LEFT JOIN PENJADWALAN where id dosen dan id block, DAN TIPE JADWAL kemudian kita count untuk mendapatakan jumlah jadwal nya    

                    if($request->tipe_jadwal == 'SEMUA') {
      
                        $jumlah_jadwal = $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                                      ->where('jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                                      ->where('jadwal_dosens.id_block',$id_block)
                                                      ->count();

                         // SELECT COUNT PRESENSI DOSEN ,  WHERE ID DOSEN DAN ID BLOCK
                        $jumlah_hadir = Presensi::leftJoin('penjadwalans','presensi.id_jadwal','=','penjadwalans.id')
                                                  ->where('presensi.id_user',$jadwal_dosen['id_dosen'])
                                                  ->where('presensi.id_block',$id_block)
                                                  ->count();

                              // $jadwal dosen kita tambahkan LEFT JOIN PENJADWALAN where id dosen, status jadwal = 1(terlakasana) dan id block,  DAN TIPE JADWAL kemudian kita count untuk mendapatakan jumlah jadwal ny
                        $jumlah_terlaksana = $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                                          ->where('jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                                          ->where('jadwal_dosens.status_jadwal',1)
                                                          ->where('jadwal_dosens.id_block',$id_block)
                                                          ->count();

                          // $jadwal dosen kita tambahkan LEFT JOIN PENJADWALAN where id dosen, status jadwal = 0(belum_terlakasana) dan id block, DAN TIPE JADWAL kemudian kita count untuk mendapatakan jumlah jadwal nya
                        $jumlah_belum_terlaksana = $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                                                ->where('jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                                                ->where('jadwal_dosens.status_jadwal',0)
                                                                ->where('jadwal_dosens.id_block',$id_block)
                                                                ->count();

                         // $jadwal dosen kita tambahkan LEFT JOIN PENJADWALAN where id dosen, status jadwal = 2(BATAL) dan id block, DAN TIPE JADWAL kemudian kita count untuk mendapatakan jumlah jadwal nya
                        $jumlah_batal = $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                                      ->where('.jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                                      ->where('.jadwal_dosens.status_jadwal',2)
                                                      ->where('.jadwal_dosens.id_block',$id_block)
                                                      ->count();

                                // $jadwal dosen kita tambahkan LEFT JOIN PENJADWALAN  where id dosen, status jadwal = 3(digantikan) dan id block, DAN TIPE JADWAL kemudian kita count untuk mendapatakan jumlah jadwal nya
                        $jumlah_digantikan = $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                                          ->where('jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                                          ->where('jadwal_dosens.status_jadwal',3)
                                                          ->where('jadwal_dosens.id_block',$id_block)
                                                          ->count();
                                        //cara hitung persentasi kehadiran = jumlah hadir * 100 / jumlahjadwal
                    }else{

                      $jumlah_jadwal = $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                                    ->where('jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                                    ->where('jadwal_dosens.id_block',$id_block)
                                                    ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)
                                                    ->count();

                       // SELECT COUNT PRESENSI DOSEN ,  WHERE ID DOSEN DAN ID BLOCK
                      $jumlah_hadir = Presensi::leftJoin('penjadwalans','presensi.id_jadwal','=','penjadwalans.id')
                                                ->where('presensi.id_user',$jadwal_dosen['id_dosen'])
                                                ->where('presensi.id_block',$id_block)
                                                ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)
                                                ->count();

                            // $jadwal dosen kita tambahkan LEFT JOIN PENJADWALAN where id dosen, status jadwal = 1(terlakasana) dan id block,  DAN TIPE JADWAL kemudian kita count untuk mendapatakan jumlah jadwal ny
                      $jumlah_terlaksana = $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                                        ->where('jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                                        ->where('jadwal_dosens.status_jadwal',1)
                                                        ->where('jadwal_dosens.id_block',$id_block)
                                                        ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)
                                                        ->count();

                        // $jadwal dosen kita tambahkan LEFT JOIN PENJADWALAN where id dosen, status jadwal = 0(belum_terlakasana) dan id block, DAN TIPE JADWAL kemudian kita count untuk mendapatakan jumlah jadwal nya
                      $jumlah_belum_terlaksana = $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                                              ->where('jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                                              ->where('jadwal_dosens.status_jadwal',0)
                                                              ->where('jadwal_dosens.id_block',$id_block)
                                                              ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)
                                                              ->count();

                       // $jadwal dosen kita tambahkan LEFT JOIN PENJADWALAN where id dosen, status jadwal = 2(BATAL) dan id block, DAN TIPE JADWAL kemudian kita count untuk mendapatakan jumlah jadwal nya
                      $jumlah_batal = $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                                    ->where('.jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                                    ->where('.jadwal_dosens.status_jadwal',2)
                                                    ->where('.jadwal_dosens.id_block',$id_block)
                                                    ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)
                                                    ->count();

                              // $jadwal dosen kita tambahkan LEFT JOIN PENJADWALAN  where id dosen, status jadwal = 3(digantikan) dan id block, DAN TIPE JADWAL kemudian kita count untuk mendapatakan jumlah jadwal nya
                      $jumlah_digantikan = $jadwal_dosen->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                                                        ->where('jadwal_dosens.id_dosen',$jadwal_dosen['id_dosen'])
                                                        ->where('jadwal_dosens.status_jadwal',3)
                                                        ->where('jadwal_dosens.id_block',$id_block)
                                                        ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)
                                                        ->count();
                                      //cara hitung persentasi kehadiran = jumlah hadir * 100 / jumlahjadwal
                      }
 
                    $presentasi = ($jumlah_hadir * 100) / $jumlah_jadwal;

                    // isi kolom                    
                    $sheet->row(++$row,[
                        $jadwal_dosen->dosen->name,                        
                        $jumlah_jadwal,
                        $jumlah_hadir,
                        $jumlah_terlaksana,
                        $jumlah_belum_terlaksana,
                        $jumlah_batal,
                        $jumlah_digantikan,
                        round($presentasi,2)." %"                   

                    ]);

                }

            });

         })->export('xls');

    
    }


// DETAIL PRESENSI
     public function detail(Request $request)
    {
        // JIKA DOSEN == SEMUA AND TIPE JADWAL == SEMUA
        if ($request->dosen == 'semua' AND $request->tipe_jadwal == 'SEMUA') {

                $query_detail_presensi = Presensi::select('users.name AS nama_dosen','master_mata_kuliahs.nama_mata_kuliah AS nama_mata_kuliah','master_ruangans.nama_ruangan AS ruangan','penjadwalans.tipe_jadwal AS tipe_jadwal','presensi.created_at AS waktu','presensi.jarak_ke_lokasi_absen AS jarak_absen','presensi.foto AS foto')
                // SELECT NAMA DOSEN, NAMA MATA KULIAH, RUANGAN, TIPE JADWAL, WAKTU, JARAK ABSEN, FOTO
                                    ->leftJoin('users','presensi.id_user','=','users.id')// LEFT JOIN USER ON ID USER = USER.ID
                                    ->leftJoin('master_ruangans','presensi.id_ruangan','=','master_ruangans.id')// LEFT JOIN MASTER RUANGAN ON ID RUANGAN = RUANGAN.ID
                                    ->leftJoin('penjadwalans','presensi.id_jadwal','=','penjadwalans.id')// LEFT JOIN PENJADAWALAN ON ID JADWAL = PENJADWALN.ID
                                    ->leftJoin('master_mata_kuliahs','penjadwalans.id_mata_kuliah','=','master_mata_kuliahs.id')// LEFT JOIN MATA KULIAH ON ID MATA KULIAH MATAKULIAH.ID
                                    ->where('presensi.id_block',$request->id_block)// AND ID BLOCK
                                    ->get();

        }else if ($request->dosen == 'semua' AND $request->tipe_jadwal != 'SEMUA') {

              $query_detail_presensi = Presensi::select('users.name AS nama_dosen','master_mata_kuliahs.nama_mata_kuliah AS nama_mata_kuliah','master_ruangans.nama_ruangan AS ruangan','penjadwalans.tipe_jadwal AS tipe_jadwal','presensi.created_at AS waktu','presensi.jarak_ke_lokasi_absen AS jarak_absen','presensi.foto AS foto')
                // SELECT NAMA DOSEN, NAMA MATA KULIAH, RUANGAN, TIPE JADWAL, WAKTU, JARAK ABSEN, FOTO
                                    ->leftJoin('users','presensi.id_user','=','users.id')// LEFT JOIN USER ON ID USER = USER.ID
                                    ->leftJoin('master_ruangans','presensi.id_ruangan','=','master_ruangans.id')// LEFT JOIN MASTER RUANGAN ON ID RUANGAN = RUANGAN.ID
                                    ->leftJoin('penjadwalans','presensi.id_jadwal','=','penjadwalans.id')// LEFT JOIN PENJADAWALAN ON ID JADWAL = PENJADWALN.ID
                                    ->leftJoin('master_mata_kuliahs','penjadwalans.id_mata_kuliah','=','master_mata_kuliahs.id')// LEFT JOIN MATA KULIAH ON ID MATA KULIAH MATAKULIAH.ID
                                    ->where('presensi.id_block',$request->id_block)// AND ID BLOCK
                                    ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)// AND TIPE JADWAL
                                    ->get();

        }else if ($request->dosen != 'semua' AND $request->tipe_jadwal == 'SEMUA') {
          
              $query_detail_presensi = Presensi::select('users.name AS nama_dosen','master_mata_kuliahs.nama_mata_kuliah AS nama_mata_kuliah','master_ruangans.nama_ruangan AS ruangan','penjadwalans.tipe_jadwal AS tipe_jadwal','presensi.created_at AS waktu','presensi.jarak_ke_lokasi_absen AS jarak_absen','presensi.foto AS foto')
                // SELECT NAMA DOSEN, NAMA MATA KULIAH, RUANGAN, TIPE JADWAL, WAKTU, JARAK ABSEN, FOTO
                                    ->leftJoin('users','presensi.id_user','=','users.id')// LEFT JOIN USER ON ID USER = USER.ID
                                    ->leftJoin('master_ruangans','presensi.id_ruangan','=','master_ruangans.id')// LEFT JOIN MASTER RUANGAN ON ID RUANGAN = RUANGAN.ID
                                    ->leftJoin('penjadwalans','presensi.id_jadwal','=','penjadwalans.id')// LEFT JOIN PENJADAWALAN ON ID JADWAL = PENJADWALN.ID
                                    ->leftJoin('master_mata_kuliahs','penjadwalans.id_mata_kuliah','=','master_mata_kuliahs.id')// LEFT JOIN MATA KULIAH ON ID MATA KULIAH MATAKULIAH.ID
                                    ->where('presensi.id_block',$request->id_block)// AND ID BLOCK
                                    ->where('presensi.id_user',$request->dosen)// AND ID USER
                                    ->get();
        }
        else{
                   

                $query_detail_presensi = Presensi::select('users.name AS nama_dosen','master_mata_kuliahs.nama_mata_kuliah AS nama_mata_kuliah','master_ruangans.nama_ruangan AS ruangan','penjadwalans.tipe_jadwal AS tipe_jadwal','presensi.created_at AS waktu','presensi.jarak_ke_lokasi_absen AS jarak_absen','presensi.foto AS foto')
                // SELECT NAMA DOSEN, NAMA MATA KULIAH, RUANGAN, TIPE JADWAL, WAKTU, JARAK ABSEN, FOTO
                                    ->leftJoin('users','presensi.id_user','=','users.id')// LEFT JOIN USER ON ID USER = USER.ID
                                    ->leftJoin('master_ruangans','presensi.id_ruangan','=','master_ruangans.id')// LEFT JOIN MASTER RUANGAN ON ID RUANGAN = RUANGAN.ID
                                    ->leftJoin('penjadwalans','presensi.id_jadwal','=','penjadwalans.id')// LEFT JOIN PENJADAWALAN ON ID JADWAL = PENJADWALN.ID
                                    ->leftJoin('master_mata_kuliahs','penjadwalans.id_mata_kuliah','=','master_mata_kuliahs.id')// LEFT JOIN MATA KULIAH ON ID MATA KULIAH MATAKULIAH.ID
                                    ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)// WHERE TIPE JADWAL;
                                    ->where('presensi.id_block',$request->id_block)// AND ID BLOCK
                                    ->where('presensi.id_user',$request->dosen)// AND ID USER
                                    ->get();
        }

        return Datatables::of($query_detail_presensi)
                        // edit kolom nama dosen
                        ->editColumn('nama_dosen', function ($presensi_dosen) {
                                // ambil nama dosen
                        return $presensi_dosen['nama_dosen'];
                        })
                        ->editColumn('tipe_jadwal', function ($presensi_dosen) {
                                // ambil TIPE  JADWAL
                        return $presensi_dosen['tipe_jadwal'];
                        })
                        ->editColumn('mata_kuliah', function ($presensi_dosen) {
                        // NAMA MATA KULIAH
                        return $presensi_dosen['nama_mata_kuliah'];
                        })
                        ->editColumn('ruangan', function ($presensi_dosen) {
                                // ambil RUANGAN
                        return $presensi_dosen['ruangan'];
                        })                             
                        ->editColumn('waktu', function ($presensi_dosen) {
                      // WAKTU
                        return $presensi_dosen['waktu'];                              
                        }) 
                        ->editColumn('jarak_ke_lokasi_absen', function ($presensi_dosen) {
                        // JARAK ABSEN
                         return $presensi_dosen['jarak_absen'] . " m"; 
                        })

                        ->editColumn('foto', function ($presensi_dosen) {  
                          // RETURN VIEW KE BLASDE FOTO
                         return view('lap_presensi_dosen.foto',[
                           'foto' => $presensi_dosen['foto'] ]);

                        })->make(true);
    }


    public function export_detail(Request $request){
       // JIKA DOSEN == SEMUA AND TIPE JADWAL == SEMUA
        if ($request->dosen == 'semua' AND $request->tipe_jadwal == 'SEMUA') {

                $query_detail_presensi = Presensi::select('users.name AS nama_dosen','master_mata_kuliahs.nama_mata_kuliah AS nama_mata_kuliah','master_ruangans.nama_ruangan AS ruangan','penjadwalans.tipe_jadwal AS tipe_jadwal','presensi.created_at AS waktu','presensi.jarak_ke_lokasi_absen AS jarak_absen','presensi.foto AS foto')
                // SELECT NAMA DOSEN, NAMA MATA KULIAH, RUANGAN, TIPE JADWAL, WAKTU, JARAK ABSEN, FOTO
                                    ->leftJoin('users','presensi.id_user','=','users.id')// LEFT JOIN USER ON ID USER = USER.ID
                                    ->leftJoin('master_ruangans','presensi.id_ruangan','=','master_ruangans.id')// LEFT JOIN MASTER RUANGAN ON ID RUANGAN = RUANGAN.ID
                                    ->leftJoin('penjadwalans','presensi.id_jadwal','=','penjadwalans.id')// LEFT JOIN PENJADAWALAN ON ID JADWAL = PENJADWALN.ID
                                    ->leftJoin('master_mata_kuliahs','penjadwalans.id_mata_kuliah','=','master_mata_kuliahs.id')// LEFT JOIN MATA KULIAH ON ID MATA KULIAH MATAKULIAH.ID
                                    ->where('presensi.id_block',$request->id_block)// AND ID BLOCK
                                    ->get();

        }else if ($request->dosen == 'semua' AND $request->tipe_jadwal != 'SEMUA') {

              $query_detail_presensi = Presensi::select('users.name AS nama_dosen','master_mata_kuliahs.nama_mata_kuliah AS nama_mata_kuliah','master_ruangans.nama_ruangan AS ruangan','penjadwalans.tipe_jadwal AS tipe_jadwal','presensi.created_at AS waktu','presensi.jarak_ke_lokasi_absen AS jarak_absen','presensi.foto AS foto')
                // SELECT NAMA DOSEN, NAMA MATA KULIAH, RUANGAN, TIPE JADWAL, WAKTU, JARAK ABSEN, FOTO
                                    ->leftJoin('users','presensi.id_user','=','users.id')// LEFT JOIN USER ON ID USER = USER.ID
                                    ->leftJoin('master_ruangans','presensi.id_ruangan','=','master_ruangans.id')// LEFT JOIN MASTER RUANGAN ON ID RUANGAN = RUANGAN.ID
                                    ->leftJoin('penjadwalans','presensi.id_jadwal','=','penjadwalans.id')// LEFT JOIN PENJADAWALAN ON ID JADWAL = PENJADWALN.ID
                                    ->leftJoin('master_mata_kuliahs','penjadwalans.id_mata_kuliah','=','master_mata_kuliahs.id')// LEFT JOIN MATA KULIAH ON ID MATA KULIAH MATAKULIAH.ID
                                    ->where('presensi.id_block',$request->id_block)// AND ID BLOCK
                                    ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)// AND TIPE JADWAL
                                    ->get();

        }else if ($request->dosen != 'semua' AND $request->tipe_jadwal == 'SEMUA') {
          
              $query_detail_presensi = Presensi::select('users.name AS nama_dosen','master_mata_kuliahs.nama_mata_kuliah AS nama_mata_kuliah','master_ruangans.nama_ruangan AS ruangan','penjadwalans.tipe_jadwal AS tipe_jadwal','presensi.created_at AS waktu','presensi.jarak_ke_lokasi_absen AS jarak_absen','presensi.foto AS foto')
                // SELECT NAMA DOSEN, NAMA MATA KULIAH, RUANGAN, TIPE JADWAL, WAKTU, JARAK ABSEN, FOTO
                                    ->leftJoin('users','presensi.id_user','=','users.id')// LEFT JOIN USER ON ID USER = USER.ID
                                    ->leftJoin('master_ruangans','presensi.id_ruangan','=','master_ruangans.id')// LEFT JOIN MASTER RUANGAN ON ID RUANGAN = RUANGAN.ID
                                    ->leftJoin('penjadwalans','presensi.id_jadwal','=','penjadwalans.id')// LEFT JOIN PENJADAWALAN ON ID JADWAL = PENJADWALN.ID
                                    ->leftJoin('master_mata_kuliahs','penjadwalans.id_mata_kuliah','=','master_mata_kuliahs.id')// LEFT JOIN MATA KULIAH ON ID MATA KULIAH MATAKULIAH.ID
                                    ->where('presensi.id_block',$request->id_block)// AND ID BLOCK
                                    ->where('presensi.id_user',$request->dosen)// AND ID USER
                                    ->get();
        }
        else{
                   

                $query_detail_presensi = Presensi::select('users.name AS nama_dosen','master_mata_kuliahs.nama_mata_kuliah AS nama_mata_kuliah','master_ruangans.nama_ruangan AS ruangan','penjadwalans.tipe_jadwal AS tipe_jadwal','presensi.created_at AS waktu','presensi.jarak_ke_lokasi_absen AS jarak_absen','presensi.foto AS foto')
                // SELECT NAMA DOSEN, NAMA MATA KULIAH, RUANGAN, TIPE JADWAL, WAKTU, JARAK ABSEN, FOTO
                                    ->leftJoin('users','presensi.id_user','=','users.id')// LEFT JOIN USER ON ID USER = USER.ID
                                    ->leftJoin('master_ruangans','presensi.id_ruangan','=','master_ruangans.id')// LEFT JOIN MASTER RUANGAN ON ID RUANGAN = RUANGAN.ID
                                    ->leftJoin('penjadwalans','presensi.id_jadwal','=','penjadwalans.id')// LEFT JOIN PENJADAWALAN ON ID JADWAL = PENJADWALN.ID
                                    ->leftJoin('master_mata_kuliahs','penjadwalans.id_mata_kuliah','=','master_mata_kuliahs.id')// LEFT JOIN MATA KULIAH ON ID MATA KULIAH MATAKULIAH.ID
                                    ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)// WHERE TIPE JADWAL;
                                    ->where('presensi.id_block',$request->id_block)// AND ID BLOCK
                                    ->where('presensi.id_user',$request->dosen)// AND ID USER
                                    ->get();
        }


           Excel::create("LAPORAN DETAIL PRESENSI DOSEN", function($excel) use ($query_detail_presensi) {

            $excel->sheet("LAPORAN DETAIL PRESENSI DOSEN", function ($sheet) use ($query_detail_presensi) {

                $sheet->loadView('lap_presensi_dosen.export_detail_presensi',['presensi'=>$query_detail_presensi]);
  

            });

         })->export('xls');

    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */ 
    public function show($id)
    {
        if ($request->dosen == 'semua') {
                    // SELECT JADWAL DOSEN WHERE ID BLOCK = $request->id_block dan kita with dosen
                 $query_detail_presensi = Jadwal_dosen::with(['dosen','ruangan','mata_kuliah'])
                                    ->where('id_block',$request->id_block)
                                    ->get();
                                    
                        // DATA YANG DIAMBIL TANGGAL,WAKTU MULAI, WAKTU SELESAI, NAMA MATA KULIAH, RUANGAN, LATITUDE , LONGITUDE, TIPE JADWAL
        }else{
                    // SELECT JADWAL DOSEN WHERE ID BLOCK = $request->id_block dan kita with dosen
                $query_detail_presensi = Jadwal_dosen::with(['dosen','ruangan','mata_kuliah'])
                                    ->where('id_block',$request->id_block)->where('id_dosen',$request->dosen)
                                    ->get();
        }

        return Datatables::of($query_detail_presensi)
                        // edit kolom nama dosen
                        ->editColumn('nama_dosen', function ($presensi_dosen) {
                                // ambil nama dosen
                        return $presensi_dosen->dosen->name;
                        })
                        ->editColumn('tipe_jadwal', function ($presensi_dosen) {
                                // ambil nama dosen
                        $penjadwalan = Penjadwalan::select('tipe_jadwal')->where('id',$presensi_dosen->id_jadwal)->first();
                        return $penjadwalan->tipe_jadwal;
                        })
                        ->editColumn('mata_kuliah', function ($presensi_dosen) {
   
                        return $presensi_dosen->mata_kuliah->nama_mata_kuliah;
                        })
                        ->editColumn('ruangan', function ($presensi_dosen) {
                                // ambil nama dosen
                        return $presensi_dosen->ruangan->nama_ruangan;
                        })
                                         // EDIT KOLOM JUMLAH JADWAL
                        ->editColumn('hadir', function ($presensi_dosen) {
                            // $jadwal dosen kita tambahkan where id dosen dan id block, kemudian kita count untuk mendapatakan jumlah jadwal nya
                           $kehadiran = Presensi::where('id',$presensi_dosen->id_jadwal)->where('id_user',$presensi_dosen->id_dosen)->count();

                           if ($kehadiran == 0) {
                                   
                            return "Tidak Hadir"; 

                           } else{

                            return "Hadir";
                           } 

                        }) 
                                                                 // EDIT KOLOM JUMLAH JADWAL
                        ->editColumn('waktu', function ($presensi_dosen) {
                            // $jadwal dosen kita tambahkan where id dosen dan id block, kemudian kita count untuk mendapatakan jumlah jadwal nya

                         $presensi = Presensi::select('created_at')->where('id',$presensi_dosen->id_jadwal)->where('id_user',$presensi_dosen->id_dosen);

                            if ($presensi->count() == 0) {
                                
                                return "-";
                            }else{
                                return $presensi->first()->created_at;  
                            }
 

                        }) 
                                                            // edit kolom jumlah_hadir
                        ->editColumn('jarak_ke_lokasi_absen', function ($presensi_dosen) {
                            // SELECT COUNT PRESENSI DOSEN ,  WHERE ID DOSEN DAN ID BLOCK

                         $presensi = Presensi::select('jarak_ke_lokasi_absen')->where('id',$presensi_dosen->id_jadwal)->where('id_user',$presensi_dosen->id_dosen);

                            if ($presensi->count() == 0) {
                                
                                return "-";
                            }else{
                                return $presensi->first()->jarak_ke_lokasi_absen . " m";  
                            }
                        })

                        ->editColumn('foto', function ($presensi_dosen) {  

                          $presensi = Presensi::select('foto')->where('id',$presensi_dosen->id_jadwal)->where('id_user',$presensi_dosen->id_dosen);

                            if ($presensi->count() == 0) {
                                
                                return  "-";

                            }else{
                                $foto = $presensi->first()->foto;  

                                return view('lap_presensi_dosen.foto',[
                                'foto' => $foto]);
                            }   

                        })->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
           // SELECT JADWAL DOSEN WHERE ID BLOCK = $request->id_block dan kita with dosen
        $jadwal_dosens = Jadwal_dosen::with(['dosen'])->where('id_block',$request->id_block)->groupBy('id_dosen')->get();


            return Datatables::of($jadwal_dosens)
                        // edit kolom nama dosen
                        ->editColumn('nama_dosen', function ($jadwal_dosen) use ($request) {
                                // ambil nama dosen
                        return $jadwal_dosen->dosen->name;
                        })
                                         // EDIT KOLOM JUMLAH JADWAL
                        ->editColumn('jumlah_jadwal', function ($jadwal_dosen) use ($request) {
                            // $jadwal dosen kita tambahkan where id dosen dan id block, kemudian kita count untuk mendapatakan jumlah jadwal nya
                         return $jadwal_dosen->where('id_dosen',$jadwal_dosen->id_dosen)->where('id_block',$request->id_block)->count();
                        }) 
                               // edit kolom jumlah_hadir
                        ->editColumn('jumlah_hadir', function ($jadwal_dosen) use ($request) {
                            // SELECT COUNT PRESENSI DOSEN ,  WHERE ID DOSEN DAN ID BLOCK
                         return Presensi::where('id_user',$jadwal_dosen->id_dosen)->where('id_block',$request->id_block)->count();
                        })
                        // EDIT KOLOM JUMLAH JADWAL TERLAKSANA
                        ->editColumn('terlaksana', function ($jadwal_dosen) use ($request) {
                            // $jadwal dosen kita tambahkan where id dosen, status jadwal = 1(terlakasana) dan id block, kemudian kita count untuk mendapatakan jumlah jadwal nya
                        return $jadwal_dosen->where('id_dosen',$jadwal_dosen->id_dosen)->where('status_jadwal',1)->where('id_block',$request->id_block)->count();
                        })
                        // EDIT KOLOM JUMLAH JADWAL BELUM TERLAKSANA
                        ->editColumn('belum_terlaksana', function ($jadwal_dosen) use ($request) {
                            // $jadwal dosen kita tambahkan where id dosen, status jadwal = 0(belum_terlakasana) dan id block, kemudian kita count untuk mendapatakan jumlah jadwal nya
                        return $jadwal_dosen->where('id_dosen',$jadwal_dosen->id_dosen)->where('status_jadwal',0)->where('id_block',$request->id_block)->count();
                        })
                        // EDIT KOLOM JUMLAH JADWAL BATAL TERLAKSANA
                        ->editColumn('batal', function ($jadwal_dosen) use ($request) {
                            // $jadwal dosen kita tambahkan where id dosen, status jadwal = 2(BATAL) dan id block, kemudian kita count untuk mendapatakan jumlah jadwal nya
                        return $jadwal_dosen->where('id_dosen',$jadwal_dosen->id_dosen)->where('status_jadwal',2)->where('id_block',$request->id_block)->count();
                        })
                        // EDIT KOLOM JUMLAH JADWAL digantikan
                        ->editColumn('digantikan', function ($jadwal_dosen) use ($request) {
                            // $jadwal dosen kita tambahkan where id dosen, status jadwal = 3(digantikan) dan id block, kemudian kita count untuk mendapatakan jumlah jadwal nya
                        return $jadwal_dosen->where('id_dosen',$jadwal_dosen->id_dosen)->where('status_jadwal',3)->where('id_block',$request->id_block)->count();
                        })

                        ->editColumn('presentasi', function ($jadwal_dosen) use ($request) {

                           // SELECT COUNT PRESENSI DOSEN ,  WHERE ID DOSEN DAN ID BLOCK
                           $jumlah_hadir = Presensi::where('id_user',$jadwal_dosen->id_dosen)->where('id_block',$request->id_block)->count();
                            // $jadwal dosen kita tambahkan where id dosen dan id block, kemudian kita count untuk mendapatakan jumlah jadwal nya
                           $jumlah_jadwal = $jadwal_dosen->where('id_dosen',$jadwal_dosen->id_dosen)->where('id_block',$request->id_block)->count();

                           //cara hitung persentasi kehadiran = jumlah hadir * 100 / jumlahjadwal
                           $presentasi = ($jumlah_hadir * 100) / $jumlah_jadwal;

                                // kita bulatkan 2 angka dibelakang koma hasil presentasinya dengan mengunakan fungsi count(,2)
                        return round($presentasi,2)." %";
                        })


            ->make(true);
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
         if ($request->dosen == 'semua') {
                // SELECT JADWAL DOSEN WHERE ID BLOCK = $request->id_block dan kita with dosen

        $jadwal_dosens = Jadwal_dosen::select('users.name AS nama_dosen','jadwal_dosens.id_dosen AS id_dosen')
                        ->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                        ->leftJoin('users','jadwal_dosens.id_dosen','=','users.id')
                        ->where('jadwal_dosens.id_block',$request->id_block)
                        ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)
                        ->groupBy('jadwal_dosens.id_dosen')->get();
      }else{

        $jadwal_dosens = Jadwal_dosen::select('users.name AS nama_dosen','jadwal_dosens.id_dosen AS id_dosen')
                        ->leftJoin('penjadwalans','jadwal_dosens.id_jadwal','=','penjadwalans.id')
                        ->leftJoin('users','jadwal_dosens.id_dosen','=','users.id')
                        ->where('jadwal_dosens.id_block',$request->id_block)
                        ->where('penjadwalans.tipe_jadwal',$request->tipe_jadwal)
                        ->where('jadwal_dosens.id_dosen',$request->dosen)
                        ->groupBy('jadwal_dosens.id_dosen')->get();
      }

            return Datatables::of($jadwal_dosens)
                        // edit kolom nama dosen
                        ->editColumn('nama_dosen', function ($jadwal_dosen) use ($request) {
                                // ambil nama dosen
                        return $jadwal_dosen['nama_dosen'];
                        })
                                         // EDIT KOLOM JUMLAH JADWAL
                        ->editColumn('jumlah_jadwal', function ($jadwal_dosen) use ($request) {
                            // $jadwal dosen kita tambahkan where id dosen dan id block, kemudian kita count untuk mendapatakan jumlah jadwal nya
                         return $jadwal_dosen->where('id_dosen',$jadwal_dosen['id_dosen'])->where('id_block',$request->id_block)->count();
                        }) 
                               // edit kolom jumlah_hadir
                        ->editColumn('jumlah_hadir', function ($jadwal_dosen) use ($request) {
                            // SELECT COUNT PRESENSI DOSEN ,  WHERE ID DOSEN DAN ID BLOCK
                         return Presensi::where('id_user',$jadwal_dosen['id_dosen'])->where('id_block',$request->id_block)->count();
                        })
                        // EDIT KOLOM JUMLAH JADWAL TERLAKSANA
                        ->editColumn('terlaksana', function ($jadwal_dosen) use ($request) {
                            // $jadwal dosen kita tambahkan where id dosen, status jadwal = 1(terlakasana) dan id block, kemudian kita count untuk mendapatakan jumlah jadwal nya
                        return $jadwal_dosen->where('id_dosen',$jadwal_dosen['id_dosen'])->where('status_jadwal',1)->where('id_block',$request->id_block)->count();
                        })
                        // EDIT KOLOM JUMLAH JADWAL BELUM TERLAKSANA
                        ->editColumn('belum_terlaksana', function ($jadwal_dosen) use ($request) {
                            // $jadwal dosen kita tambahkan where id dosen, status jadwal = 0(belum_terlakasana) dan id block, kemudian kita count untuk mendapatakan jumlah jadwal nya
                        return $jadwal_dosen->where('id_dosen',$jadwal_dosen['id_dosen'])->where('status_jadwal',0)->where('id_block',$request->id_block)->count();
                        })
                        // EDIT KOLOM JUMLAH JADWAL BATAL TERLAKSANA
                        ->editColumn('batal', function ($jadwal_dosen) use ($request) {
                            // $jadwal dosen kita tambahkan where id dosen, status jadwal = 2(BATAL) dan id block, kemudian kita count untuk mendapatakan jumlah jadwal nya
                        return $jadwal_dosen->where('id_dosen',$jadwal_dosen['id_dosen'])->where('status_jadwal',2)->where('id_block',$request->id_block)->count();
                        })
                        // EDIT KOLOM JUMLAH JADWAL digantikan
                        ->editColumn('digantikan', function ($jadwal_dosen) use ($request) {
                            // $jadwal dosen kita tambahkan where id dosen, status jadwal = 3(digantikan) dan id block, kemudian kita count untuk mendapatakan jumlah jadwal nya
                        return $jadwal_dosen->where('id_dosen',$jadwal_dosen['id_dosen'])->where('status_jadwal',3)->where('id_block',$request->id_block)->count();
                        })

                        ->editColumn('presentasi', function ($jadwal_dosen) use ($request) {

                           // SELECT COUNT PRESENSI DOSEN ,  WHERE ID DOSEN DAN ID BLOCK
                           $jumlah_hadir = Presensi::where('id_user',$jadwal_dosen['id_dosen'])->where('id_block',$request->id_block)->count();
                            // $jadwal dosen kita tambahkan where id dosen dan id block, kemudian kita count untuk mendapatakan jumlah jadwal nya
                           $jumlah_jadwal = $jadwal_dosen->where('id_dosen',$jadwal_dosen['id_dosen'])->where('id_block',$request->id_block)->count();

                           //cara hitung persentasi kehadiran = jumlah hadir * 100 / jumlahjadwal
                           $presentasi = ($jumlah_hadir * 100) / $jumlah_jadwal;

                                // kita bulatkan 2 angka dibelakang koma hasil presentasinya dengan mengunakan fungsi count(,2)
                        return round($presentasi,2)." %";
                        })


            ->make(true);
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
    }
}
