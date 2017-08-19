@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="breadcrumb">
					<li><a href="{{ url('/home') }} ">Home</a></li>
					<li><a href="{{ url('/admin/angkatan') }}">Angkatan</a></li>
					<li class="active">Edit Angkatan</li>
				</ul>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h2 class="panel-title">Edit Angkatan</h2>
					</div>

					<div class="panel-body">
						{!! Form::model($angkatan, ['url' => route('angkatan.update', $angkatan->id), 'method' => 'put', 'files'=>'true','class'=>'form-horizontal']) !!}
						@include('angkatan._form')
						{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
	