@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ul class="breadcrumb">
				<li><a href="{{ url('/home') }} ">Home</a></li>
				<li><a href="{{ url('/admin/penjadwalans') }}">Penjadwalan</a></li>
				<li class="active">Tambah Penjadwalan CSL</li>
			</ul>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Tambah Penjadwalan CSL</h2>
				</div>

				<div class="panel-body">
					<!-- MENAMPILKAN DATA PENJDWALAN-->
					{!! Form::open(['url' => route('penjadwalans.store_csl'),'method' => 'post', 'class'=>'form-horizontal']) !!}
					@include('penjadwalans_csl._form')
					{!! Form::close() !!}
					<!-- //MENAMPILKAN DATA PENJDWALAN-->
				</div>
			</div>
		</div>
	</div>
</div>
@endsection


@section('scripts')

<script type="text/javascript">
	$(document).ready(function(){		
				$('.datepicker-jadwal').datepicker({
			    format: 'yyyy-mm-dd',
			    autoclose: true,
			}); 
	});
</script>
@endsection