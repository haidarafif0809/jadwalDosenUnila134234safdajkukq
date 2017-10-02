
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
           <td>{{$detail_presensi->nama_mata_kuliah}}</td>
           <td>{{$detail_presensi->ruangan}}</td>
           <td>{{$detail_presensi->waktu}}</td>
           <td>{{$detail_presensi->jarak_absen}} m</td>
           <td><img src="{{ public_path().'/'. ($detail_presensi->foto)}}"  ></td> 
        </tr>
        @endforeach


    </tbody>

</table> 
