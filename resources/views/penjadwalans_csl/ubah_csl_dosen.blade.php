@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="breadcrumb">
					<li><a href="{{ url('/home') }} ">Home</a></li>
					<li><a href="{{ url('/admin/penjadwalans') }}">Ubah Dosen</a></li>
					<li class="active">Edit Ubah Dosen {{$penjadwalans->tipe_jadwal}}</li>
				</ul>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h2 class="panel-title">Edit Ubah Dosen {{$penjadwalans->tipe_jadwal}}</h2>
					</div>

					<div class="panel-body">    
					<!-- MENAMPILKAN DATA PENJDWALAN SESUAI ID YANG DI KIRIM -->
						{!! Form::model($penjadwalans, ['url' => route('penjadwalans.proses_ubah_dosen_csl_tutorial', $penjadwalans->id), 'method' => 'put', 'files'=>'true','class'=>'form-horizontal']) !!}
						@include('penjadwalans_csl._form_edit')
						{!! Form::close() !!}
					<!-- //MENAMPILKAN DATA PENJDWALAN SESUAI ID YANG DI KIRIM -->
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
	 
@section('scripts')
<script type="text/javascript">
	$('.js-selectize-multi-edit').selectize({
	  sortField: 'text',
	  delimiter: ',',
	  maxItems: null,
	  items: [<?php echo  $data_dosen; ?>]
	});
</script>


<script type="text/javascript">
	$(document).ready(function(){		
				$('.datepicker-jadwal').datepicker({
			    format: 'yyyy-mm-dd',
			    autoclose: true,
			}); 
	});
</script>
@endsection 