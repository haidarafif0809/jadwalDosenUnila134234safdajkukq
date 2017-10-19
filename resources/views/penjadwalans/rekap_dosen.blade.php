@extends('layouts.app')
@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <ul class="breadcrumb">
        <li><a href="{{ url('/home') }}" style="color: blue">Home</a></li>
        <li class="active">Presensi Dosen</li>
      </ul>
 
      
      <div class="panel panel-default">
        <div class="panel-heading">
          <h2 class="panel-title">Presensi Dosen</h2>
        </div>

        <div class="panel-body">                   

                   <!-- tombol export excel  DOSEN YANG SUDAH HADIR-->
                  <a href="{{ route('penjadwalans.export_rekap_dosen_hadir',$id) }}" class="btn btn-warning"><span class="glyphicon glyphicon-export"></span>Export Excel</a><br><br>

                  <center><h1>DAFTAR HADIR DOSEN</h1></center><br>

                  <div class="row">
                        <div class="col-sm-8">
                            <table>
                              <tbody>

                                <tr><td width="50%">Tipe Jadwal</td> <td> :</td> <td>{{ $tipe_jadwal }}</td></tr>

                                @if ($tipe_jadwal == "CSL" OR $tipe_jadwal == "TUTORIAL" ) 
                                    <tr><td width="50%">Mata Kuliah / Materi</td> <td> :</td><td>{{ $data_jadwal->materi->nama_materi}}</td></tr>
                                @else
                                    <tr><td width="50%">Mata Kuliah / Materi</td> <td> :</td><td>{{ $data_jadwal->mata_kuliah->nama_mata_kuliah}}</td></tr>
                                @endif  

                                <tr><td width="50%">Block</td> <td> :</td> <td>{{ $data_jadwal->block->nama_block }}</td></tr>
                                <tr><td width="50%">Ruangan</td> <td> :</td> <td>{{ $data_jadwal->ruangan->nama_ruangan }}</td></tr>

                              </tbody>
                            </table>
                        </div>
                        <div class="col-sm-4">
                            <table>
                              <tbody>
                                  <tr><td width="50%">Tanggal</td> <td> :</td> <td>{{ date('d-m-Y', strtotime($data_jadwal->tanggal)) }}</td></tr>
                                  <tr><td width="50%">Waktu</td> <td> :</td> <td>{{ $data_jadwal->waktu_mulai }} s/d {{ $data_jadwal->waktu_selesai }}</td></tr>
                              </tbody>
                            </table>                                                                                                                        
                        </div>
                    </div>
                     
                    <hr>


                   <div class="table-responsive" id="table_detail_hadir">
                      <table class="table table-bordered" id="table_detail_presensi_hadir">
                        <thead>
                          <tr>

                            <th>Nama Dosen</th>
                            <th>Tipe Jadwal</th>
                            <th>Materi / Mata Kuliah</th>
                            <th>Ruangan</th>
                            <th>Waktu Absen</th>                                            
                            <th>Jarak Absen</th>
                            <th>Foto</th>
                               
                          </tr>
                        </thead>
                     </table>
                   </div>
                   <br><br>

                                    <!-- tombol export excel  DOSEN YANG BELUM HADIR-->
                  <a href="{{ route('penjadwalans.export_rekap_dosen_belum_hadir',[$id,$tipe_jadwal]) }}" class="btn btn-warning"><span class="glyphicon glyphicon-export"></span>Export Excel</a><br><br>

                  <center><h1>DAFTAR TIDAK HADIR DOSEN</h1></center><br>


                  <div class="table-responsive" id="table_detail_belum_hadir">
                      <table class="table table-bordered" id="table_detail_presensi_belum_hadir">
                        <thead>
                          <tr>

                            <th>Nama Dosen</th>
                            <th>Tipe Jadwal</th>
                            <th>Materi / Mata Kuliah</th>
                            <th>Ruangan</th>
                            <th>Waktu Absen</th>                                            
                            <th>Jarak Absen</th>
                            <th>Foto</th>
                               
                          </tr>
                        </thead>
                     </table>
                   </div>


        </div>

      </div>
    </div>
  </div>
</div>
@endsection



@section('scripts')

<script>
$(function() {

// datatable   DOSEN YANG SUDAH HADIR<                   
             $('#table_detail_presensi_hadir').DataTable().destroy();
                       $('#table_detail_presensi_hadir').DataTable({
                              processing: true,
                              serverSide: true,
                                    "ajax": {
                                  url: '{{ Route("penjadwalans.datatable_dosen_hadir",$id) }}',
                                              "data": function ( d ) {
                  
                                            // d.custom = $('#myInput').val();
                                            // etc
                                        },
                                          type:'GET',
                                    'headers': {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                },
                              columns: [
                                  { data: 'nama_dosen', name: 'nama_dosen' },
                                  { data: 'tipe_jadwal', name: 'tipe_jadwal' },
                                  { data: 'mata_kuliah', name: 'mata_kuliah' },
                                  { data: 'ruangan', name: 'ruangan' },
                                  { data: 'waktu', name: 'waktu' },
                                  { data: 'jarak_ke_lokasi_absen', name: 'jarak_ke_lokasi_absen' },
                                  { data: 'foto', name: 'foto' }
                              ]
                      });

// datatable   DOSEN YANG BELUM HADIR<                   
             $('#table_detail_presensi_belum_hadir').DataTable().destroy();
                       $('#table_detail_presensi_belum_hadir').DataTable({
                              processing: true,
                              serverSide: true,
                                    "ajax": {
                                  url: '{{ Route("penjadwalans.datatable_dosen_belum_hadir",[$id,$tipe_jadwal]) }}',
                                              "data": function ( d ) {
                  
                                            // d.custom = $('#myInput').val();
                                            // etc
                                        },
                                          type:'GET',
                                    'headers': {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                },
                              columns: [
                                  { data: 'nama_dosen', name: 'nama_dosen' },
                                  { data: 'tipe_jadwal', name: 'tipe_jadwal' },
                                  { data: 'mata_kuliah', name: 'mata_kuliah' },
                                  { data: 'ruangan', name: 'ruangan' },
                                  { data: 'waktu', name: 'waktu' },
                                  { data: 'jarak_ke_lokasi_absen', name: 'jarak_ke_lokasi_absen' },
                                  { data: 'foto', name: 'foto' }
                              ]
                      });

});
</script>

@endsection



