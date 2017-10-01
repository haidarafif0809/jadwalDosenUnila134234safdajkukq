@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ul class="breadcrumb">
				<li><a href="{{ url('/home') }} ">Home</a></li>
				<li><a href="{{ url('/admin/materi') }}">Materi</a></li>
				<li class="active">Tambah Materi</li>
			</ul>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Tambah Materi</h2>
				</div>

				<div class="panel-body">
					{!! Form::open(['url' => route('materi.store'),'method' => 'post', 'class'=>'form-horizontal']) !!}
						@include('materi._form')
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
	$(document).ready(function(){
		$("#nama_materi").focus();
	});
</script>
@endsection