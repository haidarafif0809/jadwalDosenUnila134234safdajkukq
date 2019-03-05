@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset-1">

            {!! Form::open(['url' => 'home/analisa_jadwal/data/','method' => 'get', 'class'=>'form-inline']) !!}

                <div class="form-group{{ $errors->has('dari_tanggal') ? ' has-error' : '' }}">
            
                {!! Form::text('dari_tanggal', null, ['class'=>'form-control datepicker1','required','autocomplete'=>'off','id' => 'dari_tanggal','placeholder' => 'Dari Tanggal']) !!}
                {!! $errors->first('dari_tanggal', '<p class="help-block">:message</p>') !!}
            
                </div>

                <div class="form-group{{ $errors->has('sampai_tanggal') ? ' has-error' : '' }}">
            
                {!! Form::text('sampai_tanggal', null, ['class'=>'form-control datepicker1','required','autocomplete'=>'off','id' => 'sampai_tanggal','placeholder' => 'Sampai Tanggal']) !!}
                {!! $errors->first('sampai_tanggal', '<p class="help-block">:message</p>') !!}
            
                </div>

                <div class="form-group{{ $errors->has('id_block') ? ' has-error' : '' }}">

                 {!! Form::select('id_block', ['Semua'=>'Semua']+App\Master_block::pluck('nama_block','id')->all(), null, ['class'=>'form-control','id' => 'id_block','required', 'placeholder' => 'Pilih Block']) !!}
                {!! $errors->first('id_block', '<p class="help-block">:message</p>') !!}

                </div>

                <div class="form-group{{ $errors->has('tipe_jadwal') ? ' has-error' : '' }}">

                 {!! Form::select('tipe_jadwal',  ['Semua'=>'Semua','KULIAH'=>'KULIAH','CSL'=>'CSL','PLENO'=>'PLENO','TUTORIAL'=>'TUTORIAL'], null, ['class'=>'form-control','id' => 'tipe_jadwal','required', 'placeholder' => 'Pilih Tipe Jadwal']) !!}
                {!! $errors->first('tipe_jadwal', '<p class="help-block">:message</p>') !!}

                </div> 

                <div class="form-group">
            
                {!! Form::submit('Cari', ['class'=>'btn btn-primary']) !!}
            
                </div>

        {!! Form::close() !!}
        <br><br>

         @if ($agent->isMobile()) <!--jika diakses mobile -->

           <!-- /.row -->
            <div class="row">
                <div class="col-lg-5 col-xs-5 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-2">
                                    <i class="fa fa-thumbs-o-up fa-1x"></i>
                                </div>
                                <div class="col-xs-10 text-center">
                                    <div class="huge">{{ $jadwal_terlaksana }}</div>
                                    <div>Jadwal Terlaksana!</div>
                                </div>
                            </div>
                        </div>
                        <a href="#lihat_detail_terlaksana" id="lihat_table_terlaksana">
                            <div class="panel-footer">
                                <span class="pull-left">Lihat Detail</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 col-xs-5 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-2">
                                    <i class="fa fa-thumbs-o-down fa-1x"></i>
                                </div>
                                <div class="col-xs-10 text-center">
                                    <div class="huge">{{ $jadwal_belum_terlaksana }}</div>
                                    <div>Belum Terlaksana!</div>
                                </div>
                            </div>
                        </div>

                        <a href="#lihat_detail_belum_terlaksana" id="lihat_table_belum_terlaksana">
                            <div class="panel-footer">
                                <span class="pull-left">Lihat Detail</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 col-xs-5 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-2">
                                    <i class="fa fa-times fa-1x"></i>
                                </div>
                                <div class="col-xs-10 text-center">
                                    <div class="huge">{{ $jadwal_batal }}</div>
                                    <div>Jadwal Batal!</div>
                                </div>
                            </div>
                        </div>

                        <a href="#lihat_detail_batal" id="lihat_table_batal">
                            <div class="panel-footer">
                                <span class="pull-left">Lihat Detail</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 col-xs-5 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-2">
                                    <i class="fa fa-edit fa-1x"></i>
                                </div>
                                <div class="col-xs-10 text-center">
                                    <div class="huge">{{ $jadwal_ubah_dosen }}</div>
                                    <div>Dosen Di Gantikan</div>
                                </div>
                            </div>
                        </div>

                        <a href="#lihat_detail_batal" id="lihat_table_ubah_dosen">
                            <div class="panel-footer">
                                <span class="pull-left">Lihat Detail</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>

            </div>

        @else 
           <!-- /.row -->

            <div class="row">
                <div class="col-lg-3 col-xs-4 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-thumbs-o-up fa-4x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">{{ $jadwal_terlaksana }}</div>
                                    <div>Jadwal Terlaksana!</div>
                                </div>
                            </div>
                        </div>
                        <a href="#lihat_detail_terlaksana" id="lihat_table_terlaksana">
                            <div class="panel-footer">
                                <span class="pull-left">Lihat Detail</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-4 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-thumbs-o-down fa-4x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">{{ $jadwal_belum_terlaksana }}</div>
                                    <div>Belum Terlaksana!</div>
                                </div>
                            </div>
                        </div>
                        <a href="#lihat_detail_belum_terlaksana" id="lihat_table_belum_terlaksana">
                            <div class="panel-footer">
                                <span class="pull-left">Lihat Detail</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-4 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-times fa-4x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">{{ $jadwal_batal }}</div>
                                    <div>Jadwal Batal!</div>
                                </div>
                            </div>
                        </div>
                        <a href="#lihat_detail_batal" id="lihat_table_batal">
                            <div class="panel-footer">
                                <span class="pull-left">Lihat Detail</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-4 col-md-6">
                    <div class="panel panel-yellow">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-edit fa-4x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">{{ $jadwal_ubah_dosen }}</div>
                                    <div>Dosen Di Gantikan</div>
                                </div>
                            </div>
                        </div>
                        <a href="#lihat_detail_batal" id="lihat_table_ubah_dosen">
                            <div class="panel-footer">
                                <span class="pull-left">Lihat Detail</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>

            </div>

            @endif 



                        <div class="table-responsive" style="display:none" id="tampil_table_terlaksana">
                                 <table class="table table-bordered" id="table-terlaksana">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Mulai</th>
                                            <th>Selesai</th>
                                            <th>Tipe Jadwal</th>
                                            <th>BLock</th>
                                            <th>Mata Kuliah</th>
                                            <th>Ruangan</th>
                                            <th>Dosen</th>
                                        </tr>
                                    </thead>
                                </table>
                        </div>


                        <div class="table-responsive" style="display:none" id="tampil_table_belum_terlaksana">
                                 <table class="table table-bordered" id="table-belum-terlaksana">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Mulai</th>
                                            <th>Selesai</th>
                                            <th>Tipe Jadwal</th>
                                            <th>BLock</th>
                                            <th>Mata Kuliah</th>
                                            <th>Ruangan</th>
                                            <th>Dosen</th>
                                        </tr>
                                    </thead>
                                </table>
                        </div>

                         <div class="table-responsive" style="display:none" id="tampil_table_batal">
                                 <table class="table table-bordered" id="table-batal">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Mulai</th>
                                            <th>Selesai</th>
                                            <th>Tipe Jadwal</th>
                                            <th>BLock</th>
                                            <th>Mata Kuliah</th>
                                            <th>Ruangan</th>
                                            <th>Dosen</th>
                                        </tr>
                                    </thead>
                                </table>
                        </div>

                         <div class="table-responsive" style="display:none" id="tampil_table_ubah_dosen">
                                 <table class="table table-bordered" id="table-ubah-dosen">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Mulai</th>
                                            <th>Selesai</th>
                                            <th>Tipe Jadwal</th>
                                            <th>BLock</th>
                                            <th>Mata Kuliah</th>
                                            <th>Ruangan</th>
                                            <th>Dosen</th>
                                        </tr>
                                    </thead>
                                </table>
                        </div>
                
        </div>
    </div>
</div>
@endsection


@section('scripts')

<script>
$(function() {

    $(document).on('click','#lihat_table_terlaksana',function(){

        $("#tampil_table_terlaksana").show();
        $("#tampil_table_belum_terlaksana").hide();
        $("#tampil_table_batal").hide();

        $('#table-terlaksana').DataTable().destroy();
         $('#table-terlaksana').DataTable({
                processing: true,
                serverSide: true,
                      "ajax": {
                    url: '{{ Url("/home/table_terlaksana")}}',
                                "data": function ( d ) {
                              d.dari_tanggal = $("#dari_tanggal").val();
                              d.sampai_tanggal = $("#sampai_tanggal").val();
                              d.tipe_jadwal = $("#tipe_jadwal").val();
                              d.id_block = $("#id_block").val();
                              // d.custom = $('#myInput').val();
                              // etc
                          },
                    type:'GET',
                      'headers': {
                          'X-CSRF-TOKEN': '{{ csrf_token() }}'
                      },
                  },
                columns: [
                    { data: 'tanggal', name: 'tanggal' },
                    { data: 'waktu_mulai', mulai: 'waktu_mulai' },
                    { data: 'waktu_selesai', name: 'waktu_selesai' },
                    { data: 'tipe_jadwal', name: 'tipe_jadwal' },
                    { data: 'block.nama_block', name: 'block.nama_block' },
                    { data: 'mata_kuliah.nama_mata_kuliah', name: 'mata_kuliah.nama_mata_kuliah' },
                    { data: 'ruangan.nama_ruangan', name: 'ruangan.nama_ruangan' },
                    { data: 'jadwal_dosen', name: 'jadwal_dosen' }
                ]
        });
    });
});
</script>

<script>
$(function() {


    $(document).on('click','#lihat_table_belum_terlaksana',function(){

        $("#tampil_table_terlaksana").hide();
        $("#tampil_table_belum_terlaksana").show();
        $("#tampil_table_batal").hide();
        $("#tampil_table_ubah_dosen").hide();

    $('#table-belum-terlaksana').DataTable().destroy();
    $('#table-belum-terlaksana').DataTable({
        processing: true,
        serverSide: true,
                 "ajax": {
            url: '{{ Url("home/table_belum_terlaksana") }}',
                        "data": function ( d ) {
                      d.dari_tanggal = $("#dari_tanggal").val();
                      d.sampai_tanggal = $("#sampai_tanggal").val();
                      d.tipe_jadwal = $("#tipe_jadwal").val();
                      d.id_block = $("#id_block").val();
                      // d.custom = $('#myInput').val();
                      // etc
                  },
            type:'GET',
              'headers': {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
          },
        columns: [
            { data: 'tanggal', name: 'tanggal' },
            { data: 'waktu_mulai', mulai: 'waktu_mulai' },
            { data: 'waktu_selesai', name: 'waktu_selesai' },
            { data: 'tipe_jadwal', name: 'tipe_jadwal' },
            { data: 'block.nama_block', name: 'block.nama_block' },
            { data: 'mata_kuliah.nama_mata_kuliah', name: 'mata_kuliah.nama_mata_kuliah' },
            { data: 'ruangan.nama_ruangan', name: 'ruangan.nama_ruangan' },
            { data: 'jadwal_dosen', name: 'jadwal_dosen' }
        ]
    });
    });
});
</script>

<script>
$(function() {

    $(document).on('click','#lihat_table_batal',function(){

        $("#tampil_table_terlaksana").hide();
        $("#tampil_table_belum_terlaksana").hide();
        $("#tampil_table_batal").show();
        $("#tampil_table_ubah_dosen").hide();

    $('#table-batal').DataTable().destroy();

    $('#table-batal').DataTable({
        processing: true,
        serverSide: true,
                 "ajax": {
            url: '{{ Url("/home/table_batal") }}',
                        "data": function ( d ) {
                      d.dari_tanggal = $("#dari_tanggal").val();
                      d.sampai_tanggal = $("#sampai_tanggal").val();
                      d.tipe_jadwal = $("#tipe_jadwal").val();
                      d.id_block = $("#id_block").val();
                      // d.custom = $('#myInput').val();
                      // etc
                  },
            type:'GET',
              'headers': {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
          },
        columns: [
            { data: 'tanggal', name: 'tanggal' },
            { data: 'waktu_mulai', mulai: 'waktu_mulai' },
            { data: 'waktu_selesai', name: 'waktu_selesai' },
            { data: 'tipe_jadwal', name: 'tipe_jadwal' },
            { data: 'block.nama_block', name: 'block.nama_block' },
            { data: 'mata_kuliah.nama_mata_kuliah', name: 'mata_kuliah.nama_mata_kuliah' },
            { data: 'ruangan.nama_ruangan', name: 'ruangan.nama_ruangan' },
            { data: 'jadwal_dosen', name: 'jadwal_dosen' }
        ]
     });    
    });
});
</script>

<script>
$(function() {

    $(document).on('click','#lihat_table_ubah_dosen',function(){

        $("#tampil_table_terlaksana").hide();
        $("#tampil_table_belum_terlaksana").hide();
        $("#tampil_table_batal").hide();
        $("#tampil_table_ubah_dosen").show();

    $('#table-ubah-dosen').DataTable().destroy();

    $('#table-ubah-dosen').DataTable({
        processing: true,
        serverSide: true,
                 "ajax": {
            url: '{{ Url("/home/table_ubah_dosen") }}',
                        "data": function ( d ) {
                      d.dari_tanggal = $("#dari_tanggal").val();
                      d.sampai_tanggal = $("#sampai_tanggal").val();
                      d.tipe_jadwal = $("#tipe_jadwal").val();
                      d.id_block = $("#id_block").val();
                      // d.custom = $('#myInput').val();
                      // etc
                  },
            type:'GET',
              'headers': {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
          },
        columns: [
            { data: 'tanggal', name: 'tanggal' },
            { data: 'waktu_mulai', mulai: 'waktu_mulai' },
            { data: 'waktu_selesai', name: 'waktu_selesai' },
            { data: 'tipe_jadwal', name: 'tipe_jadwal' },
            { data: 'block.nama_block', name: 'block.nama_block' },
            { data: 'mata_kuliah.nama_mata_kuliah', name: 'mata_kuliah.nama_mata_kuliah' },
            { data: 'ruangan.nama_ruangan', name: 'ruangan.nama_ruangan' },
            { data: 'jadwal_dosen', name: 'jadwal_dosen' }
        ]
     });    
    });
});
</script>

@endsection

