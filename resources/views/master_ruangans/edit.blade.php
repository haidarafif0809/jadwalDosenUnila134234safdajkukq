@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="breadcrumb">
					<li><a href="{{ url('/home') }} ">Home</a></li>
					<li><a href="{{ url('/admin/master_ruangans') }}">Ruangan</a></li>
					<li class="active">Edit Ruangan</li>
				</ul>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h2 class="panel-title">Edit Ruangan</h2>
					</div>

					<div class="panel-body">
						{!! Form::model($master_ruangans, ['url' => route('master_ruangans.update', $master_ruangans->id), 'method' => 'put', 'files'=>'true','class'=>'form-horizontal']) !!}
						@include('master_ruangans._form')
						{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
	