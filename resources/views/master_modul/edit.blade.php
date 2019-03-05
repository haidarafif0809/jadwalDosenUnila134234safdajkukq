@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="breadcrumb">
					<li><a href="{{ url('/home') }} ">Home</a></li>
					<li><a href="{{ url('/admin/modul') }}">Block</a></li>
					<li class="active">Edit Block</li>
				</ul>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h2 class="panel-title">Edit Block</h2>
					</div>

					<div class="panel-body">
						{!! Form::model($modul, ['url' => route('modul.update', $modul->id), 'method' => 'put', 'files'=>'true','class'=>'form-horizontal']) !!}
						@include('master_modul._form')
						{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
	