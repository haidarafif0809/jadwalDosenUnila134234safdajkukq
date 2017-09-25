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
					<h2 class="panel-title">Lap. Rekap Presensi Dosen</h2>
				</div>

				<div class="panel-body">
<br>
					   <div class="row">
						    <div class="col-md-2">


						        <div class="form-group{{ $errors->has('id_block') ? ' has-error' : '' }}">

                         <select class="form-control" id="id_block" required name="id_block">

                              <option value=""> Silahkan Pilih</option>
                              @foreach($master_blocks AS $block)
                              <option value="{{ $block->id }} - {{ $block->nama_block }}"> {{ $block->nama_block }}</option>
                              @endforeach
                   
                          </select>

						        </div>
						    </div>


						    <div class="col-md-1">
						        <div class="form-group">
						            
                        <button  class="btn btn-primary" id="tampil_rekap"> <span class="glyphicon glyphicon-th-list"></span> Tampil</button>
						            
						        </div>
			          </div>

                <div class="col-md-1">
                    <div class="form-group">
                        
                            {!! Form::open(['url' => route('laporan_rekap_presensi_dosen.export'),'method' => 'post', 'class'=>'form-inline']) !!}
            
                               {!! Form::text('block', null, ['class'=>'form-control','autocomplete'=>'off','id' => 'block', 'style'=>'display:none']) !!}
                              <button id="export_excel" class="btn btn-warning" style="display:none"> <span class="glyphicon glyphicon-export"></span> Export Excel</button> 


                             {!! Form::close() !!}
                        
                    </div>
                </div>


			       </div>

			            <center><h1 style="display: none" id="text_judul"></h1></center><br>

			            <div class="table-responsive" style="display:none" id="tampil_table_presensi_dosen">
                                 <table class="table table-bordered" id="table_presensi_dosen">
                                    <thead>
                                        <tr>

                                            <th>Nama Dosen</th>
                                            <th>Jumlah Jadwal</th>                                            
                                            <th>Jumlah Hadir</th>
                                            <th>Terlaksana</th>
                                            <th>Belum Terlaksana</th>
                                            <th>Batal</th>
                                            <th>Digantikan</th>
                                            <th>Presentasi Kehadiran (%)</th>
                               
                                        </tr>
                                    </thead>
                                </table>
                  </div>
<br>
                <h6 id="keterangan" style="text-align: left ; color: red ; display: none"><i> * Presentasi Kehadiran = (Jumlah Hadir * 100) / Jumlah Jadwal</i></h6>


				</div>

			</div>
		</div>
	</div>
</div>
@endsection


@section('scripts')

<script>
$(function() {

    $(document).on('click','#tampil_rekap',function(){

      // master block
    	var master_blocks = $("#id_block").val();
      var blocks = master_blocks.split(" - ");// master block split
      var id_block = blocks[0];// split yang 0 adalah id block
      var nama_block = blocks[1];// split yang pertama adalah nama block

      $("#export_excel").show();// show tombol export excel
    	$("#text_judul").show();// show text judul laporan rekap
      $("#keterangan").show();// show keterangan
    	$("#text_judul").text("REKAP DAFTAR HADIR DOSEN BLOCK "+ nama_block); // kita ubah isi text judul nya
      $("#block").val(id_block);// kita isi input block dengan id block

    	$("#tampil_table_presensi_dosen").show();// show table
  
  // datatable
        $('#table_presensi_dosen').DataTable().destroy();
         $('#table_presensi_dosen').DataTable({
                processing: true,
                serverSide: true,
                      "ajax": {
                    url: '{{ route("laporan_rekap_presensi_dosen.store") }}',
                                "data": function ( d ) {
                              d.id_block = id_block;
                              // d.custom = $('#myInput').val();
                              // etc
                          },
                    type:'POST',
                      'headers': {
                          'X-CSRF-TOKEN': '{{ csrf_token() }}'
                      },
                  },
                columns: [
                    { data: 'dosen.name', name: 'dosen.name' },
                    { data: 'jumlah_jadwal', mulai: 'jumlah_jadwal' },
                    { data: 'jumlah_hadir', mulai: 'jumlah_hadir' },
                    { data: 'terlaksana', name: 'terlaksana' },
                    { data: 'belum_terlaksana', name: 'belum_terlaksana' },
                    { data: 'batal', name: 'batal' },
                    { data: 'digantikan', name: 'digantikan' },
                    { data: 'presentasi', name: 'presentasi' }
                ]
        });
    });



});
</script>

@endsection


