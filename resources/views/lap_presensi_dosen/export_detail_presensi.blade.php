<table>
   <thead>

        <tr><td>Tipe Jadwal</td> : <td>{{$data_jadwal->tipe_jadwal}} </td></tr><br>

        @if($data_jadwal->tipe_jadwal == 'CSL' || $data_jadwal->tipe_jadwal == 'TUTORIAL')
           <tr><td>Mata Kuliah / Materi</td> : <td>{{$data_jadwal->materi->nama_materi}} </td></tr><br>
        @else 
           <tr><td>Mata Kuliah / Materi</td> : <td>{{$data_jadwal->mata_kuliah->nama_mata_kuliah}} </td></tr><br>
        @endif 
        
        <tr><td>Block</td> : <td>{{$data_jadwal->block->nama_block}} </td></tr><br>
        <tr><td>Ruangan</td> : <td>{{$ruangan}} </td></tr><br>
        <tr><td>Tanggal</td> : <td>{{date('d-m-Y', strtotime($data_jadwal->tanggal))}} </td></tr><br>
        <tr><td>Waktu</td> : <td>{{$data_jadwal->waktu_mulai}} s/d {{$data_jadwal->waktu_selesai}} </td></tr><br>

     
    <tr></tr>

    <tr>
        <th>Nama Dosen</th>
        <th>Tipe Jadwal</th>
        <th>Mata Kuliah</th>
        <th>Ruangan</th>
        <th>Waktu Absen</th>                                            
        <th>Jarak Absen</th>
        <th>Foto</th>
                               
    </tr>
   </thead>
    <tbody>

        @foreach($presensi AS $detail_presensi)
        <tr>
           <td>{{$detail_presensi->nama_dosen}}</td>
           <td>{{$detail_presensi->tipe_jadwal}}</td>

           <!-- JIKA TIPE JADWALNYA CSL ATAU TUTORIAL -->
           @if($detail_presensi->tipe_jadwal == 'CSL' || $detail_presensi->tipe_jadwal == 'TUTORIAL')
           <td>{{ App\Materi::select('nama_materi')->where('id',$detail_presensi->id_materi)->first()->nama_materi }} </td><!-- MATERI -->
           @else 
           <td>{{$detail_presensi->nama_mata_kuliah}}</td><!-- NAMA MATA KULIAH -->
           @endif 

           <td>{{$ruangan}}</td> <!-- RUANGAN -->
           <td>{{$detail_presensi->waktu}}</td> <!-- WAKTU ABSEN -->
           <td>{{$detail_presensi->jarak_absen}} m</td><!-- JARAK ABSEN -->
           <td><img src="{{ public_path().'/'. ($detail_presensi->foto)}}"  ></td> <!-- FOTO ABSEN -->
        </tr>
        @endforeach


    </tbody>

</table>