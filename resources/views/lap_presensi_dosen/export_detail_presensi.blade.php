
 <table>
   <thead>
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

           <td>{{$detail_presensi->ruangan}}</td> <!-- RUANGAN -->
           <td>{{$detail_presensi->waktu}}</td> <!-- WAKTU ABSEN -->
           <td>{{$detail_presensi->jarak_absen}} m</td><!-- JARAK ABSEN -->
           <td><img src="{{ public_path().'/'. ($detail_presensi->foto)}}"  ></td> <!-- FOTO ABSEN -->
        </tr>
        @endforeach


    </tbody>

</table> 
