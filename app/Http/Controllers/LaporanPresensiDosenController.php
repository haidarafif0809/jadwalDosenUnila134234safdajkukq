<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use App\Jadwal_dosen;
use App\Presensi;
use App\Master_block;
use Excel;


class LaporanPresensiDosenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // SELECT MASTER BLOCK, UNTUK DIAMBIL NAMA DAN ID BLOCK NYA
        $master_blocks = Master_block::select('nama_block','id')->get();
        
        // RETURN VIEW lap_presensi_dosen.index, DAN KITA PASSING $master_blocks
        return view("lap_presensi_dosen.index",[
            'master_blocks' => $master_blocks
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
                        return round($presentasi,2);
                        })


            ->make(true);
    }// 


// export excel
        public function export_excel(Request $request)
    {
        $id_block = $request->block;
        // SELECT JADWAL DOSEN WHERE ID BLOCK = $request->id_block dan kita with dosen
        $jadwal_dosens = Jadwal_dosen::with(['dosen'])->where('id_block',$id_block)->groupBy('id_dosen')->get();
     
         Excel::create("REKAP PRESENSI DOSEN", function($excel) use ($id_block, $jadwal_dosens) {

            $excel->sheet("REKAP PRESENSI DOSEN", function ($sheet) use ($id_block, $jadwal_dosens) {

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
                               // $jadwal dosen kita tambahkan where id dosen dan id block, kemudian kita count untuk mendapatakan jumlah jadwal nya       
                    $jumlah_jadwal = $jadwal_dosen->where('id_dosen',$jadwal_dosen->id_dosen)->where('id_block',$id_block)->count();
                            // SELECT COUNT PRESENSI DOSEN ,  WHERE ID DOSEN DAN ID BLOCK
                    $jumlah_hadir = Presensi::where('id_user',$jadwal_dosen->id_dosen)->where('id_block',$id_block)->count();
                          // $jadwal dosen kita tambahkan where id dosen, status jadwal = 1(terlakasana) dan id block, kemudian kita count untuk mendapatakan jumlah jadwal ny
                    $jumlah_terlaksana = $jadwal_dosen->where('id_dosen',$jadwal_dosen->id_dosen)->where('status_jadwal',1)->where('id_block',$id_block)->count();
                      // $jadwal dosen kita tambahkan where id dosen, status jadwal = 0(belum_terlakasana) dan id block, kemudian kita count untuk mendapatakan jumlah jadwal nya
                    $jumlah_belum_terlaksana = $jadwal_dosen->where('id_dosen',$jadwal_dosen->id_dosen)->where('status_jadwal',0)->where('id_block',$id_block)->count();
                            // $jadwal dosen kita tambahkan where id dosen, status jadwal = 2(BATAL) dan id block, kemudian kita count untuk mendapatakan jumlah jadwal nya
                    $jumlah_batal = $jadwal_dosen->where('id_dosen',$jadwal_dosen->id_dosen)->where('status_jadwal',2)->where('id_block',$id_block)->count();
                            // $jadwal dosen kita tambahkan where id dosen, status jadwal = 3(digantikan) dan id block, kemudian kita count untuk mendapatakan jumlah jadwal nya
                    $jumlah_digantikan = $jadwal_dosen->where('id_dosen',$jadwal_dosen->id_dosen)->where('status_jadwal',3)->where('id_block',$id_block)->count();
                                    //cara hitung persentasi kehadiran = jumlah hadir * 100 / jumlahjadwal
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
                        round($presentasi,2)                   

                    ]);

                }

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
