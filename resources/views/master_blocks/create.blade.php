@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ul class="breadcrumb">
				<li><a href="{{ url('/home') }} ">Home</a></li>
				<li><a href="{{ url('/admin/master_blocks') }}">Block</a></li>
				<li class="active">Tambah Block</li>
			</ul>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Tambah Block</h2>
				</div>

				<div class="panel-body">
					{!! Form::open(['url' => route('master_blocks.store'),'method' => 'post', 'class'=>'form-horizontal']) !!}
						@include('master_blocks._form')
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection