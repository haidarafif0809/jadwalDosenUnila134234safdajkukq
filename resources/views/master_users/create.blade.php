@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ul class="breadcrumb">
				<li><a href="{{ url('/home') }} ">Home</a></li>
				<li><a href="{{ url('/admin/master_users') }}">User</a></li>
				<li class="active">Tambah User</li>
			</ul>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Tambah User</h2>
				</div>

				<div class="panel-body">
					{!! Form::open(['url' => route('master_users.store'),'method' => 'post', 'class'=>'form-horizontal']) !!}
					@include('master_users._form')
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts') 
<script type="text/javascript"> 
	$(".role-id").change(function(){ 
		var is_mahasiswa = 0;
		var role_id = $(this).val();
	
		for (var i = 0; i < role_id.length; i++) {
		 
			if (role_id[i] == 3) {
				is_mahasiswa = 1;
			} 
		}
		if (is_mahasiswa == 1){ 
			$("#data_angkatan").show();
		}
		else { 
			$("#data_angkatan").hide();
		}
	});
</script>
@endsection