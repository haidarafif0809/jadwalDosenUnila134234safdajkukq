@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ul class="breadcrumb">
				<li><a href="{{ url('/home') }} ">Home</a></li>
			</ul>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Jadwal Kuliah Mahasiswa</h2>
				</div>

				<div class="panel-body">
					{!! Form::open(['url' => route('jadwal.mahasiswa'),'method' => 'get', 'class'=>'form-horizontal']) !!}
						@include('mahasiswa._form')
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>

@include('mahasiswa.schedule_mahasiswa');


@endsection

@section('scripts')

<script type="text/javascript">
	$("#block").change(function(){

		var $select = $("#modul").selectize();

		var selectize = $select[0].selectize.destroy();


		var block = $(this).val();

		$.post('{{ route('modul.data_modul_perblock')}}',{
			 '_token': $('meta[name=csrf-token]').attr('content'),
			block:block},
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


		var block = $("#block").val();

		$.post('{{ route('modul.data_modul_perblock')}}',{
			 '_token': $('meta[name=csrf-token]').attr('content'),
			block:block},
			function(data){
			$('#modul')
			    .find('option')
			    .remove();
			$("#modul").append(data);
			$("#modul").selectize();


		});


		$('.single-event').click(function(){
			var id_jadwal = $(this).attr('data-id');
			$.post('{{ route('jadwal.info')}}',{

			 '_token': $('meta[name=csrf-token]').attr('content'),
			 'id_jadwal' :id_jadwal
			},function(data){
				$('.isi-event').html(data);
			});
			
		});
	});
</script>
@endsection