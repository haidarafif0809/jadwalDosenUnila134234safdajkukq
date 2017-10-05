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

<!--LAPORAN REKAP KULIAH, PLENO, PRAKTIKUM -->
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
                                    <th>Persentase</th>
                                    <th>Keterangan</th>

                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

<!--LAPORAN REKAP CSL DAN TUTORIAL -->
                <div class="col-md-12" style="display: none" id="laporan_presensi_csl_tutor"><hr>
                <center><h1>REKAP DAFTAR HADIR MAHASISWA</h1></center><br>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="table-laporan-csl-tutor">
                            <thead>
                                <tr>
                                    <th>NPM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Jumlah Jadwal</th>
                                    <th>Jumlah Hadir</th>
                                    <th>Persentase</th>
                                    <th>Keterangan</th>

                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

<!--LAPORAN DETAIL -->
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

<!--LAPORAN REKAP SEMUA TANPA TIPE JADWAL  -->
                <div class="col-md-12" style="display: none" id="laporan_presensi_rekap_semua"><hr>
                <center><h1>DETAIL DAFTAR HADIR MAHASISWA</h1></center><br>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="table-laporan-rekap-semua">
                            <thead>
                                <tr>
                                    <th>NPM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Csl</th>
                                    <th>Kuliah</th>
                                    <th>Pleno</th>
                                    <th>Praktikum</th>
                                    <th>Tutorial</th>
                                    <th>Keterangan</th>

                                </tr>
                            </thead>
                        </table>
                    </div>
                    <h6 style="text-align: left ; color: red; font-style: italic;">**Note : (-) Artinya Tipe Jadwal Tersebut Belum Ada Penjadwalan Atau Penjadwalan Belum Terlaksana</h6>
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

    //FILTER REKAP SEMUA TIPE JADWAL
        if(tipe_jadwal == "" && id_kelompok == "" && mahasiswa == ""){
            
            //LAPORAN DETAIL
            $('#table-laporan-rekap-semua').DataTable().destroy();
            $('#table-laporan-rekap-semua').DataTable({
                processing: true,
                serverSide: true,
                         "ajax": {
                    url: '{{ route("laporan_presensi_mahasiswa.proses_laporan_rekap_semua") }}', "data": function ( d ) {
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
                    { data: 'persentase_csl', name: 'persentase_csl', class: 'angka' },
                    { data: 'persentase_kuliah', name: 'persentase_kuliah', class: 'angka' },
                    { data: 'persentase_pleno', name: 'persentase_pleno', class: 'angka' },
                    { data: 'persentase_praktikum', name: 'persentase_praktikum', class: 'angka' },
                    { data: 'persentase_tutorial', name: 'persentase_tutorial', class: 'angka' },
                    { data: 'keterangan', name: 'keterangan' }
                ]
            });

            $("#laporan_presensi_rekap_semua").show();
            $("#laporan_presensi_csl_tutor").hide();
            $("#laporan_presensi_detail").hide();
            $("#laporan_presensi").hide();
            $("#btnExcel").show();

            if (tipe_jadwal == "") {
                tipe_jadwal = 0;
            };
            if (mahasiswa == "") {
                mahasiswa = 0;
            };
            if (id_kelompok == "") {
                id_kelompok = 0;
            };

            $("#btnExcel").attr("href", "laporan_presensi_mahasiswa/download_lap_rekap_semua_presensi/"+id_block+"/"+jenis_laporan+"/"+tipe_jadwal+"/"+mahasiswa+"/"+id_kelompok+"");

        }
    //JIKA ADA TIPE JADWAL YG DIPILIH
        else{

            if (tipe_jadwal == "CSL" || tipe_jadwal == "TUTORIAL") {


        //LAPORAN REKAP CSL DAN TUTORIAL
                $('#table-laporan-csl-tutor').DataTable().destroy();
                $('#table-laporan-csl-tutor').DataTable({
                    processing: true,
                    serverSide: true,
                             "ajax": {
                        url: '{{ route("laporan_presensi_mahasiswa.proses_laporan_rekap_csl_tutor") }}', "data": function ( d ) {
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
                        { data: 'jumlah_jadwal', name: 'jumlah_jadwal', class: 'angka' },
                        { data: 'jumlah_hadir', name: 'jumlah_hadir', class: 'angka' },
                        { data: 'presentase', name: 'presentase', class: 'angka' },
                        { data: 'keterangan', name: 'keterangan' }
                    ]
                });

                $("#laporan_presensi_rekap_semua").hide();
                $("#laporan_presensi_csl_tutor").show();
                $("#laporan_presensi").hide();
                $("#laporan_presensi_detail").hide();
                $("#btnExcel").show();
                
                if (tipe_jadwal == "") {
                    tipe_jadwal = 0;
                };
                if (mahasiswa == "") {
                    mahasiswa = 0;
                };
                if (id_kelompok == "") {
                    id_kelompok = 0;
                };

                $("#btnExcel").attr("href", "laporan_presensi_mahasiswa/download_lap_rekap_csl_tutor_presensi/"+id_block+"/"+jenis_laporan+"/"+tipe_jadwal+"/"+mahasiswa+"/"+id_kelompok+"");

            }
            else{

        //LAPORAN REKAP KULIAH, PLENO DAN PRAKTIKUM
                $('#table-laporan').DataTable().destroy();
                $('#table-laporan').DataTable({
                    processing: true,
                    serverSide: true,
                             "ajax": {
                        url: '{{ route("laporan_presensi_mahasiswa.proses_laporan_rekap") }}', "data": function ( d ) {
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
                        { data: 'jumlah_jadwal', name: 'jumlah_jadwal', class: 'angka' },
                        { data: 'jumlah_hadir', name: 'jumlah_hadir', class: 'angka' },
                        { data: 'presentase', name: 'presentase', class: 'angka' },
                        { data: 'keterangan', name: 'keterangan' }
                    ]
                });

                $("#laporan_presensi_rekap_semua").hide();
                $("#laporan_presensi_csl_tutor").hide();
                $("#laporan_presensi").show();
                $("#laporan_presensi_detail").hide();
                $("#btnExcel").show();
                
                if (tipe_jadwal == "") {
                    tipe_jadwal = 0;
                };
                if (mahasiswa == "") {
                    mahasiswa = 0;
                };
                if (id_kelompok == "") {
                    id_kelompok = 0;
                };

                $("#btnExcel").attr("href", "laporan_presensi_mahasiswa/download_lap_rekap_presensi/"+id_block+"/"+jenis_laporan+"/"+tipe_jadwal+"/"+mahasiswa+"/"+id_kelompok+"");
            }

        }

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

        $("#laporan_presensi_rekap_semua").hide();
        $("#laporan_presensi_csl_tutor").hide();
        $("#laporan_presensi_detail").show();
        $("#laporan_presensi").hide();
        $("#btnExcel").show();

        if (tipe_jadwal == "") {
            tipe_jadwal = 0;
        };
        if (mahasiswa == "") {
            mahasiswa = 0;
        };
        if (id_kelompok == "") {
            id_kelompok = 0;
        };

        $("#btnExcel").attr("href", "laporan_presensi_mahasiswa/download_lap_detail_presensi/"+id_block+"/"+jenis_laporan+"/"+tipe_jadwal+"/"+mahasiswa+"/"+id_kelompok+"");

    }

});

    $("#form_rekap_mahasiswa").submit(function(){
        return false;
    });
</script>
@endsection