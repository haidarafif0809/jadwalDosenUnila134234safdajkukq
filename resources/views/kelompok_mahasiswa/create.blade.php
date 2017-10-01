@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ul class="breadcrumb">
				<li><a href="{{ url('/home') }} ">Home</a></li>
				<li><a href="{{ route('kelompok_mahasiswa.index') }}">Kelompok Mahasiswa</a></li>
				<li class="active">Tambah Kelompok Mahasiswa</li>
			</ul>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Tambah Kelompok Mahasiswa</h2>
				</div>

				<div class="panel-body">
					{!! Form::open(['url' => route('kelompok_mahasiswa.store'),'method' => 'post', 'class'=>'form-horizontal']) !!}
						@include('kelompok_mahasiswa._form')
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection	