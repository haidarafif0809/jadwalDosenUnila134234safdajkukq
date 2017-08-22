@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ul class="breadcrumb">
				<li><a href="{{ url('/home') }} ">Home</a></li>
				<li><a href="{{ url('/admin/penjadwalans') }}">Penjadwalan</a></li>
				<li class="active">Tambah Penjadwalan</li>
			</ul>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Tambah Penjadwalan</h2>
				</div>

				<div class="panel-body">
					{!! Form::open(['url' => route('penjadwalans.store'),'method' => 'post', 'class'=>'form-horizontal']) !!}
					@include('penjadwalans._form')
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection


@section('scripts')

<script type="text/javascript">
	$("#id_block").change(function(){

		var $select = $("#modul").selectize();

		var selectize = $select[0].selectize.destroy();


		var id_block = $(this).val();

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
	$(document).ready(function(){
		var $select = $("#modul").selectize();

		var selectize = $select[0].selectize.destroy();


		var id_block = $("#id_block").val();
		if (id_block != '') {

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
	$("#modul").change(function(){
 

		var id_modul = $(this).val();

		$.post('{{ route('modul.tanggal_modul_perblock_penjadwalan')}}',{
			 '_token': $('meta[name=csrf-token]').attr('content'),
			id_modul:id_modul},
			function(data){
		var res = data.split(","); 

		var date = new Date(res[0]);
		var end_date = new Date(res[1]);
		// use this to allow certain dates only



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