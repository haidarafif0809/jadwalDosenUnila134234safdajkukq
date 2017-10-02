@extends('layouts.app')
@section('content')

<style type="text/css">
    .angka {
        text-align: right;
    }
    .tengah {
        text-align: center;
    }
    .keterangan {
        background-color: #4CAF50;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <ul class="breadcrumb">
                <li><a href="{{ url('/home') }}">Home</a></li>
                <li class="active">Laporan Presensi Mahasiswa</li>
            </ul>
 
            
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">Laporan Presensi Mahasiswa</h2>
                </div>

                <div class="panel-body">                

                {!! Form::open(['method' => 'post', 'class'=>'form-group', 'id'=>'form_rekap_mahasiswa', 'class'=>'form-inline']) !!}

                    <div class="form-group{{ $errors->has('id_block') ? ' has-error' : '' }}">
                        {!! Form::select('jenis_laporan', [
                        '1'=>'Laporan Rekap',
                        '0'=>'Laporan Detail'
                        ], null, ['class'=>'form-control js-selectize-reguler', 'placeholder' => '--JENIS LAPORAN--','id'=>'jenis_laporan', 'required' => '']) !!}
                        {!! $errors->first('jenis_laporan', '<p class="help-block">:message</p>') !!}
                    </div>
                    
                    <div class="form-group{{ $errors->has('id_block') ? ' has-error' : '' }}">
                        {!! Form::select('id_block', App\Master_block::pluck('nama_block','id')->all(), null, ['class'=>'form-control js-selectize-reguler', 'placeholder' => '--PILIH BLOCK--', 'id' => 'block']) !!}
                        {!! $errors->first('id_block', '<p class="help-block">:message</p>') !!}                              
                    </div>
                    
                    <div class="form-group{{ $errors->has('tipe_jadwal') ? ' has-error' : '' }}">
                        {!! Form::select('tipe_jadwal', ['KULIAH'=>'KULIAH','PRAKTIKUM'=>'PRAKTIKUM','CSL'=>'CSL','PLENO'=>'PLENO','TUTORIAL'=>'TUTORIAL'], null, ['class'=>'form-control js-selectize-reguler', 'placeholder' => '--TIPE JADWAL--','id' => 'tipe_jadwal']) !!}
                        {!! $errors->first('tipe_jadwal', '<p class="help-block">:message</p>') !!}                            
                    </div>
                    
                    <div class="form-group{{ $errors->has('id_kelompok') ? ' has-error' : '' }}" id="div_kelompok" style="display: none">
                        {!! Form::select('id_kelompok', App\KelompokMahasiswa::pluck('nama_kelompok_mahasiswa','id')->all(), null, ['class'=>'form-control js-selectize-reguler', 'placeholder' => '--PILIH KELOMPOK--', 'id' => 'kelompok']) !!}
                        {!! $errors->first('id_kelompok', '<p class="help-block">:message</p>') !!}                              
                    </div>
                    
                    <div class="form-group{{ $errors->has('mahasiswa') ? ' has-error' : '' }}">
                        {!! Form::select('mahasiswa', $mahasiswa, null, ['class'=>'form-control js-selectize-reguler', 'placeholder' => '--MAHASISWA--', 'id' => 'mahasiswa']) !!}
                        {!! $errors->first('mahasiswa', '<p class="help-block">:message</p>') !!}                            
                    </div>

                    <div class="form-group">
                        <button id="tampil_laporan" class="btn btn-primary"> <span class="glyphicon glyphicon-eye-open"></span> Tampil</button>

                        <!-- MEMBUAT TOMBOL EXPORT EXCEL -->
                        <a href='#' style="display: none" class='btn btn-warning' id="btnExcel" target='blank'><span class="glyphicon glyphicon-export"></span> Export Excel</a>
                    </div>

                {!! Form::close() !!}

                <div class="col-md-12" style="display: none" id="laporan_presensi"><hr>
                <center><h1>REKAP DAFTAR HADIR MAHASISWA</h1></center><br>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="table-laporan">
                            <thead>
                                <tr>
                                    <th>NPM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Jumlah Jadwal</th>
                                    <th>Jumlah Hadir</th>
                                    <th>Presentase</th>
                                    <th>Keterangan</th>

                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div class="col-md-12" style="display: none" id="laporan_presensi_detail"><hr>
                <center><h1>DETAIL DAFTAR HADIR MAHASISWA</h1></center><br>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="table-laporan-detail">
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
                </div>

                </div><!-- PANEL-BODY -->
            </div>
        </div>
    </div>

</div> <!--CONTAINER -->
@endsection

@section('scripts')

<script>
$(document).on('change','#tipe_jadwal',function(){

    var tipe_jadwal = $("#tipe_jadwal").val();

    if (tipe_jadwal == "CSL" || tipe_jadwal == "TUTORIAL") {
        $("#div_kelompok").show();
    }
    else{
        $("#div_kelompok").hide();
    }
});
</script>

<script>
$(document).on('click','#tampil_laporan',function(){

    var id_block = $("#block").val();
    var jenis_laporan = $("#jenis_laporan").val();
    var tipe_jadwal = $("#tipe_jadwal").val();
    var id_kelompok = $("#kelompok").val();
    var mahasiswa = $("#mahasiswa").val();

    if (jenis_laporan == "") {
        alert("Silakan Pilih Jenis Laporan !");
        $("#jenis_laporan").focus();
    }
    else if(id_block == ""){
        alert("Silakan Pilih Blok !");
        $("#block").focus();        
    }
    else if(jenis_laporan == 1){

    //LAPORAN REKAP
        $('#table-laporan').DataTable().destroy();
        $('#table-laporan').DataTable({
            processing: true,
            serverSide: true,
                     "ajax": {
                url: '{{ route("laporan_presensi_mahasiswa.proses_laporan_rekap") }}', "data": function ( d ) {
                          d.id_block = $("#block").val();
                          d.jenis_laporan = $("#jenis_laporan").val();
                          d.tipe_jadwal = $("#tipe_jadwal").val();
                          d.mahasiswa = $("#mahasiswa").val();
                          // d.custom = $('#myInput').val();
                          // etc
                      },
                type:'POST',
                  'headers': {
                      'X-CSRF-TOKEN': '{{ csrf_token() }}'
                  },
              },
            columns: [
                { data: 'email', name: 'email' },
                { data: 'name', name: 'name' },
                { data: 'jumlah_jadwal', name: 'jumlah_jadwal', class: 'angka' },
                { data: 'jumlah_hadir', name: 'jumlah_hadir', class: 'angka' },
                { data: 'presentase', name: 'presentase', class: 'angka' },
                { data: 'keterangan', name: 'keterangan' }
            ]
        });

    $("#laporan_presensi").show();
    $("#laporan_presensi_detail").hide();
    $("#btnExcel").show();
    
    if (tipe_jadwal == "") {
        tipe_jadwal = 0;
    };
    if (mahasiswa == "") {
        mahasiswa = 0;
    };

    $("#btnExcel").attr("href", "laporan_presensi_mahasiswa/download_lap_rekap_presensi/"+id_block+"/"+jenis_laporan+"/"+tipe_jadwal+"/"+mahasiswa+"");

    }
    else if(jenis_laporan == 0){

    //LAPORAN DETAIL
        $('#table-laporan-detail').DataTable().destroy();
        $('#table-laporan-detail').DataTable({
            processing: true,
            serverSide: true,
                     "ajax": {
                url: '{{ route("laporan_presensi_mahasiswa.proses_laporan_detail") }}', "data": function ( d ) {
                          d.id_block = $("#block").val();
                          d.jenis_laporan = $("#jenis_laporan").val();
                          d.tipe_jadwal = $("#tipe_jadwal").val();
                          d.id_kelompok = $("#kelompok").val();
                          d.mahasiswa = $("#mahasiswa").val();
                          // d.custom = $('#myInput').val();
                          // etc
                      },
                type:'POST',
                  'headers': {
                      'X-CSRF-TOKEN': '{{ csrf_token() }}'
                  },
              },
            columns: [
                { data: 'email', name: 'email' },
                { data: 'name', name: 'name' },
                { data: 'nama_materi', name: 'nama_materi' },
                { data: 'nama_ruangan', name: 'nama_ruangan' },
                { data: 'waktu', name: 'waktu' },
                { data: 'jarak_absen', name: 'jarak_absen' },
                { data: 'foto', name: 'foto' },
                { data: 'keterangan', name: 'keterangan' }
            ]
        });

    $("#laporan_presensi_detail").show();
    $("#laporan_presensi").hide();
    $("#btnExcel").show();

    if (tipe_jadwal == "") {
        tipe_jadwal = 0;
    };
    if (mahasiswa == "") {
        mahasiswa = 0;
    };

    $("#btnExcel").attr("href", "laporan_presensi_mahasiswa/download_lap_detail_presensi/"+id_block+"/"+jenis_laporan+"/"+tipe_jadwal+"/"+mahasiswa+"");

    }

});

    $("#form_rekap_mahasiswa").submit(function(){
        return false;
    });
</script>
@endsection