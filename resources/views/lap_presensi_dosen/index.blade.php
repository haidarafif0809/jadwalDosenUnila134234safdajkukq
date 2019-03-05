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
					<h2 class="panel-title">Lap. Presensi Dosen</h2>
				</div>

				<div class="panel-body">
<br>
					   <div class="row">

                <div class="col-md-2">                  
                    <div class="form-group{{ $errors->has('jenis_laporan') ? ' has-error' : '' }} ">
                        {!! Form::select('jenis_laporan', ['1' => 'Rekap', '2' => 'Detail'], null, ['class'=>'form-control js-selectize-reguler', 'id' => 'jenis_laporan',
                         'placeholder' => 'Jenis Laporan']) !!}
                        {!! $errors->first('jenis_laporan', '<p class="help-block">:message</p>') !!}                    
                    </div>
                </div>

						    <div class="col-md-2">
						        <div class="form-group{{ $errors->has('id_block') ? ' has-error' : '' }}">

                         <select class="form-control js-selectize-reguler" id="id_block" required name="id_block">

                              <option value=""> -- PILIH BLOCK --</option>
                              @foreach($master_blocks AS $block)
                              <option value="{{ $block->id }} - {{ $block->nama_block }}"> {{ $block->nama_block }}</option>
                              @endforeach
                   
                          </select>

						        </div>
						    </div>

                <div class="col-md-2">                  
                  <div class="form-group{{ $errors->has('tipe_jadwal') ? ' has-error' : '' }}">
                        {!! Form::select('tipe_jadwal', ['SEMUA'=>'- SEMUA -','KULIAH'=>'KULIAH','PRAKTIKUM'=>'PRAKTIKUM','CSL'=>'CSL','PLENO'=>'PLENO','TUTORIAL'=>'TUTORIAL'], null, ['class'=>'form-control js-selectize-reguler', 'placeholder' => 'PILIH TIPE JADWAL','id' => 'tipe_jadwal']) !!}
                        {!! $errors->first('tipe_jadwal', '<p class="help-block">:message</p>') !!}
                  </div>
                </div>

                <div class="col-md-2">                  
                    <div class="form-group{{ $errors->has('dosen') ? ' has-error' : '' }} ">
                        {!! Form::select('dosen', $dosen, $value = 'semua', ['class'=>'form-control js-selectize-reguler', 'id' => 'dosen']) !!}
                        {!! $errors->first('dosen', '<p class="help-block">:message</p>') !!}                    
                    </div>
                </div>


						    <div class="col-md-1">
						        <div class="form-group">
						            
                        <button  class="btn btn-primary" id="tampil_rekap"> <span class="glyphicon glyphicon-th-list"></span> Tampil</button>
						            
						        </div>
			          </div>

                <div class="col-md-1">
                    <div class="form-group">
                        
                             <a href="" id="export_excel" class="btn btn-warning" style="display:none" target="blank"><span class="glyphicon glyphicon-export"></span>Export Excel</a>
                        
                    </div>
                </div>


			       </div>
<hr>
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
                                            <th>Persentasi Kehadiran (%)</th>
                               
                                        </tr>
                                    </thead>
                                </table>
                  </div>
<br>
                <h6 id="keterangan" style="text-align: left ; color: red ; display: none"><i> * Persentasi Kehadiran = (Jumlah Hadir * 100) / Jumlah Jadwal</i></h6>


                <span id="detail" style="display:none;">
                  
                   <div class="table-responsive" id="table_detail">
                      <table class="table table-bordered" id="table_detail_presensi">
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

                </span>


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


      var jenis_laporan = $("#jenis_laporan").val();
       // master block
      var master_blocks = $("#id_block").val();
      var blocks = master_blocks.split(" - ");// master block split
      var id_block = blocks[0];// split yang 0 adalah id block
      var nama_block = blocks[1];// split yang pertama adalah nama block
        // jika jenis lpaoran = 1 / Rekap
      var dosen = $("#dosen").val();
      var tipe_jadwal = $("#tipe_jadwal").val();

      if (jenis_laporan == "") {

        alert("Jenis Laporan belum dipilih!");
        }
      else if (master_blocks == "") {

        alert("Block belum dipilih!");

      }else if (tipe_jadwal == "") {

        alert("Tipe Jadwal belum dipilih!");
    }else{


           if (jenis_laporan == 1) {


                    $("#export_excel").show();// show tombol export excel
                    $("#text_judul").show();// show text judul laporan rekap
                    $("#keterangan").show();// show keterangan
                    $("#text_judul").text("REKAP DAFTAR HADIR DOSEN BLOCK "+ nama_block); // kita ubah isi text judul nya
                    $("#block").val(id_block);// kita isi input block dengan id block

                    $("#tampil_table_presensi_dosen").show();// show table
                    $("#table_detail").hide();// show table

                // datatable                      
                       $('#table_detail_presensi').DataTable().destroy();
                       $('#table_presensi_dosen').DataTable().destroy();
                       $('#table_presensi_dosen').DataTable({
                              processing: true,
                              serverSide: true,
                                    "ajax": {
                                  url: '{{ route("laporan_rekap_presensi_dosen.store") }}',
                                              "data": function ( d ) {
                                            d.id_block = id_block;
                                            d.dosen = dosen;
                                            d.tipe_jadwal = tipe_jadwal;
                                            // d.custom = $('#myInput').val();
                                            // etc
                                        },
                                  type:'POST',
                                    'headers': {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                },
                              columns: [
                                  { data: 'nama_dosen', name: 'nama_dosen' },
                                  { data: 'jumlah_jadwal', name: 'jumlah_jadwal' },
                                  { data: 'jumlah_hadir', name: 'jumlah_hadir' },
                                  { data: 'terlaksana', name: 'terlaksana' },
                                  { data: 'belum_terlaksana', name: 'belum_terlaksana' },
                                  { data: 'batal', name: 'batal' },
                                  { data: 'digantikan', name: 'digantikan' },
                                  { data: 'presentasi', name: 'presentasi' }
                              ]
                      });

            $("#export_excel").attr('href','export_rekap_presensi_dosen/'+dosen+'/'+id_block+'/'+tipe_jadwal);

                       // jika jenis laporan == detail
             }else if (jenis_laporan == 2) {// 

                  $("#keterangan").hide();// show keterangan
                  $("#export_excel").show();// show tombol export excel
                  $("#text_judul").show();// show text judul laporan rekap
                  $("#text_judul").text("DETAIL DAFTAR HADIR DOSEN BLOCK "+ nama_block); // kita ubah isi text judul nya
                  $("#block").val(id_block);// kita isi input block dengan id block
                  $("#detail").show();// show table
                  $("#tampil_table_presensi_dosen").hide();// show table
                  $("#table_detail").show();// show table
              
                                  // datatable
                       $('#table_detail_presensi').DataTable().destroy();
                       $('#table_presensi_dosen').DataTable().destroy();
                       $('#table_detail_presensi').DataTable({
                              processing: true,
                              serverSide: true,
                                    "ajax": {
                                  url: '{{ route("laporan_rekap_presensi_dosen.detail") }}',
                                              "data": function ( d ) {
                                            d.id_block = id_block;
                                            d.dosen = dosen;
                                            d.tipe_jadwal = tipe_jadwal;
                                            // d.custom = $('#myInput').val();
                                            // etc
                                        },
                                  type:'POST',
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

                       $("#export_excel").attr('href','export_detail_presensi_dosen/'+dosen+'/'+id_block+'/'+tipe_jadwal);

             }
      }


    


    });

});
</script>

@endsection


