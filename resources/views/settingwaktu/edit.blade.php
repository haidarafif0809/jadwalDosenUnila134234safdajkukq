@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="breadcrumb">
					<li><a href="{{ url('/home') }} ">Home</a></li>
					<li><a href="{{ url('/admin/settingwaktu') }}">Setiing Waktu</a></li>
					<li class="active">Edit Waktu</li>
				</ul>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h2 class="panel-title">Edit Waktu</h2>
					</div>

					<div class="panel-body">
						{!! Form::model($settingwaktu, ['url' => route('settingwaktu.update', $settingwaktu->id), 'method' => 'put', 'files'=>'true','class'=>'form-horizontal']) !!}
						@include('settingwaktu._form')
						{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
	