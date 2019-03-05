@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ul class="breadcrumb">
				<li><a href="{{ url('/home') }} ">Home</a></li>
				<li><a href="{{ url('/admin/setting_slide') }}">Slide</a></li>
				<li class="active">Tambah Slide</li>
			</ul>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Tambah Slide</h2>
				</div>

				<div class="panel-body">
					{!! Form::open(['url' => route('setting_slide.store'),'method' => 'post', 'class'=>'form-horizontal', 'files'=>'true']) !!}
					@include('setting_slide._form')
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
