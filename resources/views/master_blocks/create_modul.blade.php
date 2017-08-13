@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ul class="breadcrumb">
				<li><a href="{{ url('/home') }} ">Home</a></li>
				<li><a href="{{ url('/admin/master_blocks') }}">Block</a></li>
				<li class="active">Kaitkan Modul ke Block</li>
			</ul>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Kaitkan Modul ke Block</h2>
				</div>

				<div class="panel-body">
					{!! Form::open(['url' => route('master_blocks.proses_kait_modul_blok',$id),'method' => 'put', 'class'=>'form-horizontal']) !!}
						@include('master_blocks._form_modul')
					{!! Form::close() !!}
				</div>
			</div>
			<!-- panel form  -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Data Modul Terkait</h2>
				</div>

				<div class="panel-body">
					
				</div>
			</div>
			<!-- panel data  -->
		</div>
	</div>
</div>
@endsection