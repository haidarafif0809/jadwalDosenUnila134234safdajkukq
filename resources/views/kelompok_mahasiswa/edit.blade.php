@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="breadcrumb">
					<li><a href="{{ url('/home') }} ">Home</a></li>
					<li><a href="{{ url('/admin/kelompok_mahasiswa') }}">Kelompok Mahasiswa</a></li>
					<li class="active">Edit Kelompok Mahasiswa</li>
				</ul>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h2 class="panel-title">Edit Kelompok Mahasiswa</h2>
					</div>

					<div class="panel-body">
						{!! Form::model($kelompok, ['url' => route('kelompok_mahasiswa.update', $kelompok->id), 'method' => 'put', 'files'=>'true','class'=>'form-horizontal']) !!}
						@include('kelompok_mahasiswa._form')
						{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection