@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ul class="breadcrumb">
				<li><a href="{{ url('/home') }} ">Home</a></li>
				<li><a href="{{ route('penjadwalans.index') }} ">Penjadwalan</a></li>
				<li><a href="{{ route('master_blocks.modul',$modul->id_blok) }} ">Modul</a></li>
				<li class="active">Jadwal Kuliah {{ $modul->block->nama_block }} : {{ $modul->modul->nama_modul}}</li>
			</ul>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Jadwal Kuliah {{ $modul->block->nama_block }} : {{ $modul->modul->nama_modul}}</h2>
				</div>

				<div class="panel-body">
				@role('admin')
				<p> <button data-toggle="collapse" data-target="#form_penjadwalan" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Tambah Jadwal</button>
</p>
				
					<div id="form_penjadwalan" class="collapse">
					{!! Form::open(['url' => route('penjadwalans.store'),'method' => 'post', 'class'=>'form-horizontal']) !!}
										@include('penjadwalans._form')
										{!! Form::close() !!}
					</div>
				@endrole
				@role('dosen')
				<p> <button data-toggle="collapse" data-target="#form_penjadwalan" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Tambah Jadwal</button>
</p>
				
					<div id="form_penjadwalan" class="collapse">
					{!! Form::open(['url' => route('penjadwalans.store'),'method' => 'post', 'class'=>'form-horizontal']) !!}
										@include('penjadwalans._form')
										{!! Form::close() !!}
					</div>
				@endrole

					@include('mahasiswa.schedule_mahasiswa')
				</div>
			</div>
		</div>
	</div>
</div>




@endsection

@section('scripts')

<script type="text/javascript">
//MENGAMBIL ID BLOK UNTUK MENAMPILKAN MODUL YANG ADA DI BLOK YANG DI PILIH
	$("#id_block").change(function(){

		var $select = $("#modul").selectize(); 
		var selectize = $select[0].selectize.destroy(); 
		var id_block = $(this).val(); 
		//POST ID BLOCK KE CONTROLLER
		$.post('{{ route('modul.data_modul_perblock_penjadwalan')}}',{
			 '_token': $('meta[name=csrf-token]').attr('content'),
			id_block:id_block},
			function(data){
			$('#modul')
			    .find('option')
			    .remove();
			$("#modul").append(data);
			$("#modul").selectize();


		});

	});	


	$("#tipe_jadwal").change(function(){

		var tipe_jadwal = $(this).val(); 
		
		if (tipe_jadwal == 'KULIAH') {
			$(".kolom-mata-kuliah").show();
			$("#id_mata_kuliah").val("");
		}
		else if (tipe_jadwal == 'PRAKTIKUM')  {
			$(".kolom-mata-kuliah").show();
			$("#id_mata_kuliah").val("");

		}
		else {
			$(".kolom-mata-kuliah").hide();
		}


	});

	$(document).ready(function(){
		//MENAMPILKAN MODUL SESUAI DATA PENJADWALAN
		var $select = $("#modul").selectize(); 
		var selectize = $select[0].selectize.destroy(); 
		var id_block = $("#id_block").val();
		if (id_block != '') {
		//POST ID MODUL KE CONTROLLER UNTUK MENAMPILKAN PERIODE YANG ADA DI MODUL
		$.post('{{ route('modul.data_modul_perblock_penjadwalan')}}',{
			 '_token': $('meta[name=csrf-token]').attr('content'),
			id_block:id_block},
			function(data){
			$('#modul')
			    .find('option')
			    .remove();
			$("#modul").append(data);
			$("#modul").selectize();


		});
		}

	});
</script>

<script type="text/javascript">
  //MENGIRIMKAN ID MODUL KE CONTROLLER
	$("#modul").change(function(){ 
		var id_modul = $(this).val(); 
		$.post('{{ route('modul.tanggal_modul_perblock_penjadwalan')}}',{
			 '_token': $('meta[name=csrf-token]').attr('content'),
			id_modul:id_modul},
			function(data){
		var res = data.split(",");  
		var date = new Date(res[0]);
		var end_date = new Date(res[1]); 
 	//MENAMPILKAN TANGGAL SESUAI PERIODE MODUL YANG DI PILIH
			$('.datepicker-modul-jadwal').datepicker('remove');
			$('.datepicker-modul-jadwal').datepicker({
			    format: 'yyyy-mm-dd',
			    daysOfWeekDisabled: '0,6',
			    startDate: date,
			    autoclose: true,
			    endDate : end_date
			   
			});
		}); 
	}); 
</script>

@endsection