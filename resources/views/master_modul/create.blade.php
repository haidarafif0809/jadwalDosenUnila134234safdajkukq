@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ul class="breadcrumb">
				<li><a href="{{ url('/home') }} ">Home</a></li>
				<li><a href="{{ url('/admin/modul') }}">Modul</a></li>
				<li class="active">Tambah Modul</li>
			</ul>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Tambah Modul</h2>
				</div>

				<div class="panel-body">
					{!! Form::open(['url' => route('modul.store'),'method' => 'post', 'class'=>'form-horizontal']) !!}
						@include('master_modul._form')
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection