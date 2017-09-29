@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ul class="breadcrumb">
				<li><a href="{{ url('/home') }} ">Home</a></li>
				<li><a href="{{ url('/admin/kelompok_mahasiswa') }}">Kelompok Mahasiswa   </a></li>
				<li class="active">Kaitkan Mahasiswa ke {{ $kelompok->nama_kelompok_mahasiswa}}</li>
			</ul>
			@role('admin')
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Kaitkan Mahasiswa ke Kelompok Mahasiswa {{ $kelompok->nama_kelompok_mahasiswa}}</h2>
				</div>

				<div class="panel-body">
					{!! Form::open(['url' => route('kelompok_mahasiswa.proses_kait_list_kelompok_mahasiswa',$id),'method' => 'put', 'class'=>'form-horizontal']) !!}
						@include('kelompok_mahasiswa._form_mahasiswa')
					{!! Form::close() !!}
				</div>
			</div>
			@endrole
			<!-- panel form  -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Mahasiswa {{ $kelompok->nama_kelompok_mahasiswa}}</h2>
				</div>

				<div class="panel-body">
						<div class="table-responsive">
					{!! $html->table(['class'=>'table-striped table']) !!}
					</div>
				</div>
			</div>
			<!-- panel data  -->
		</div>
	</div>
</div>
@endsection


@section('scripts')
{!! $html->scripts() !!}
<script type="text/javascript">
	// confirm delete
		$(document.body).on('submit', '.js-confirm', function () {
		var $el = $(this)
		var text = $el.data('confirm') ? $el.data('confirm') : 'Anda yakin melakukan tindakan ini\
	?'
		var c = confirm(text);
		return c;
	}); 
</script>
@endsection