@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <ul class="breadcrumb">
                <li><a href="{{ url('/home') }}" style="color: blue">Home</a></li>
                <li class="active">Presensi Mahasiswa</li>
            </ul>
 
            
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">Presensi Mahasiswa</h2>
                </div>

                <div class="panel-body">

<!--LAPORAN DETAIL -->
			{!! Form::open(['method' => 'post', 'class'=>'form-group', 'id'=>'kehadiran_mahasiswa', 'class'=>'form-inline']) !!}
				{!! Form::hidden('id', $id, ['class'=>'form-control','autocomplete'=>'off', 'id'=>'id_jadwal']) !!}
				{!! Form::hidden('id_block', $id_block, ['class'=>'form-control','autocomplete'=>'off', 'id_block'=>'id_block']) !!}
				{!! Form::hidden('tipe_jadwal', $tipe_jadwal, ['class'=>'form-control','autocomplete'=>'off', 'tipe_jadwal'=>'tipe_jadwal']) !!}
			{!! Form::close() !!}


                <a href="{{ route('penjadwalans.download_mahasiswa_hadir',[$id, $id_block, $tipe_jadwal]) }}" class="btn btn-warning" id="btnExcelHadir" target="blank"><span class="glyphicon glyphicon-export"></span> Export Excel</a>

                <center><h1>DAFTAR HADIR MAHASISWA </h1></center><br>

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
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="presensi_mahasiswa">
                            <thead>
                                <tr>
                                    <th>NPM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Mata Kuliah / Materi</th>
                                    <th>Ruangan</th>
                                    <th>Waktu Absen</th>
                                    <th>Jarak Absen</th>
                                    <th>Foto</th>
                                    <th>Keterangan</th>

                                </tr>
                            </thead>
                        </table>
                    </div>

                <hr>

                <a href="{{ route('penjadwalans.download_mahasiswa_tidak_hadir',[$id, $id_block, $tipe_jadwal]) }}" class="btn btn-warning" id="btnExcelHadir" target="blank"><span class="glyphicon glyphicon-export"></span> Export Excel</a>

                <center><h1>DAFTAR TIDAK HADIR MAHASISWA</h1></center><br>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="presensi_mahasiswa_tidak">
                            <thead>
                                <tr>
                                    <th>NPM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Mata Kuliah / Materi</th>
                                    <th>Ruangan</th>
                                    <th>Waktu Absen</th>
                                    <th>Jarak Absen</th>
                                    <th>Foto</th>
                                    <th>Keterangan</th>

                                </tr>
                            </thead>
                        </table>
                    </div>

                </div><!-- PANEL-BODY -->
            </div>
        </div>
    </div>

</div> <!--CONTAINER -->
@endsection

@section('scripts')

<script>
$(document).ready( function() {
    //MAHASISWA MASUK

    var id = $("#id_jadwal").val();
    var id_block = $("#id_block").val();
    var tipe_jadwal = $("#tipe_jadwal").val();

        $('#presensi_mahasiswa').DataTable().destroy();
        $('#presensi_mahasiswa').DataTable({
            processing: true,
            serverSide: true,
                     "ajax": {
                url: '{{ route("penjadwalans.kehadiran_mahasiswa", [$id, $id_block, $tipe_jadwal]) }}', "data": function ( d ) {
                          // d.custom = $('#myInput').val();
                          // etc
                      },
                type:'GET',
                  'headers': {
                      'X-CSRF-TOKEN': '{{ csrf_token() }}'
                  },
              },
            columns: [
                { data: 'email', name: 'email' },
                { data: 'name', name: 'name' },
                { data: 'materi_kuliah', name: 'materi_kuliah' },
                { data: 'nama_ruangan', name: 'nama_ruangan' },
                { data: 'waktu', name: 'waktu' },
                { data: 'jarak_absen', name: 'jarak_absen' },
                { data: 'foto', name: 'foto' },
                { data: 'keterangan', name: 'keterangan' }
            ]
        });


        $('#presensi_mahasiswa_tidak').DataTable().destroy();
        $('#presensi_mahasiswa_tidak').DataTable({
            processing: true,
            serverSide: true,
                     "ajax": {
                url: '{{ route("penjadwalans.kehadiran_mahasiswa_absen", [$id, $id_block, $tipe_jadwal]) }}', "data": function ( d ) {
                          // d.custom = $('#myInput').val();
                          // etc
                      },
                type:'GET',
                  'headers': {
                      'X-CSRF-TOKEN': '{{ csrf_token() }}'
                  },
              },
            columns: [
                { data: 'email', name: 'email' },
                { data: 'name', name: 'name' },
                { data: 'materi_kuliah', name: 'materi_kuliah' },
                { data: 'nama_ruangan', name: 'nama_ruangan' },
                { data: 'waktu', name: 'waktu' },
                { data: 'jarak_absen', name: 'jarak_absen' },
                { data: 'foto', name: 'foto' },
                { data: 'keterangan', name: 'keterangan' }
            ]
        });


});

    $("#kehadiran_mahasiswa").submit(function(){
        return false;
    });
</script>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection