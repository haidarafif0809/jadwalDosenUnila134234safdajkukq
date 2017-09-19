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
                <li class="active">Laporan Rekap Presensi Mahasiswa</li>
            </ul>
 
            
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">Laporan Rekap Presensi Mahasiswa</h2>
                </div>

                <div class="panel-body">

                    {!! Form::open(['method' => 'post', 'class'=>'form-group', 'id'=>'form_rekap_mahasiswa']) !!}
                        <div class="form-group{{ $errors->has('id_block') ? ' has-error' : '' }}  col-md-2">
                            {!! Form::select('id_block', App\Master_block::pluck('nama_block','id')->all(), null, ['class'=>'form-control js-selectize-reguler', 'placeholder' => '--PILIH BLOCK--', 'id' => 'block']) !!}
                            {!! $errors->first('id_block', '<p class="help-block">:message</p>') !!}                    
                        </div>

                        <div class="form-group">                    
                            <button id="tampil_laporan" class="btn btn-primary"> <span class="glyphicon glyphicon-eye-open"></span> Tampil</button>             
                        </div>
                    {!! Form::close() !!}

                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="table-laporan">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NPM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Jumlah Jadwal</th>
                                    <th>Jumlah Hadir</th>
                                    <th>Presentase</th>
                                    <th>KETERANGAN</th>

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
$(document).on('click','#tampil_laporan',function(){

    $('#table-laporan').DataTable().destroy();
    $('#table-laporan').DataTable({
        processing: true,
        serverSide: true,
                 "ajax": {
            url: '{{ route("laporan_presensi_mahasiswa.proses_laporan_rekap") }}', "data": function ( d ) {
                      d.id_block = $("#block").val();
                      // d.custom = $('#myInput').val();
                      // etc
                  },
            type:'POST',
              'headers': {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
          },
        columns: [
            { data: 'no_urut', name: 'no_urut', class: 'tengah' },
            { data: 'email', name: 'email' },
            { data: 'name', name: 'name' },
            { data: 'jumlah_jadwal', name: 'jumlah_jadwal', class: 'angka' },
            { data: 'jumlah_hadir', name: 'jumlah_hadir', class: 'angka' },
            { data: 'presentase', name: 'presentase', class: 'angka' },
            { data: 'keterangan', name: 'keterangan', class: 'tengah' }
        ]
    });

});

    $("#form_rekap_mahasiswa").submit(function(){
        return false;
    });
</script>
@endsection