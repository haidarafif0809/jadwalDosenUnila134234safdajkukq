@extends('layouts.app')
@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <ul class="breadcrumb">
        <li><a href="{{ url('/home') }}">Home</a></li>
        <li class="active">Lap. Rekap Presensi Dosen</li>
      </ul>
 
      
      <div class="panel panel-default">
        <div class="panel-heading">
          <h2 class="panel-title">Rekap Presensi Dosen</h2>
        </div>

        <div class="panel-body">


                   <center><h1 >REKAP DAFTAR DOSEN YANG SUDAH HADIR</h1></center><br>

                   <!-- tombol export excel  DOSEN YANG SUDAH HADIR-->
                  <a href="{{ route('penjadwalans.export_rekap_dosen_hadir',$id) }}" class="btn btn-warning"><span class="glyphicon glyphicon-export"></span>Export Excel</a><br><br>

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

                  <center><h1 >REKAP DAFTAR DOSEN YANG BELUM HADIR</h1></center><br>

                  <!-- tombol export excel  DOSEN YANG BELUM HADIR-->
                  <a href="{{ route('penjadwalans.export_rekap_dosen_belum_hadir',[$id,$tipe_jadwal]) }}" class="btn btn-warning"><span class="glyphicon glyphicon-export"></span>Export Excel</a><br><br>

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



