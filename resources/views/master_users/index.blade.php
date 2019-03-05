@extends('layouts.app')
@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ul class="breadcrumb">
				<li><a href="{{ url('/home') }}">Home</a></li>
				<li class="active">User</li>
			</ul>
 
			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">User</h2>
				</div>

				<div class="panel-body"> 
				<div class="row">
					<div class="col-md-1" style="padding-right: 8%"> 
						 <a class="btn btn-primary" href="{{ route('master_users.create') }}">Tambah User</a>
				 	</div>
 
					<div class="col-md-1" style="padding-left: 2%"> 
						 <a class="btn btn-primary" href="{{ route('master_users.index') }}">Semua </a> 
				 	</div>

					<div class="col-md-1"  style="padding-left: 0%">
						  <div class="dropdown">
					    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">User Konfirmasi
					    <span class="caret"></span></button>
					    <ul class="dropdown-menu">
					      <li><a href="{{ url('admin/master_users/filterkonfirmasi/1') }}">Sudah Di Konfirmasi</a></li>
					      <li><a href="{{ url('admin/master_users/filterkonfirmasi/0') }}">Belum Di Konfirmasi</a></li> 
					    </ul>
					  </div>  
					</div>

					<div class="col-md-1" style="padding-left: 4%">
						  <div class="dropdown"">
					    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Mahasiswa Angkatan
					    <span class="caret"></span></button>
					    <ul class="dropdown-menu"> 
					        @foreach($angkatan as $angkatans)
							    <li><a href="{{ route('master_users.filter_angkatan',$angkatans->id) }}">{{ $angkatans->nama_angkatan }}</a></li>
							@endforeach
					    </ul>
					  </div>  
					</div>

					<div class="col-md-2">
						  <div class="dropdown" style="padding-left: 70%">
					    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Otoritas User
					    <span class="caret"></span></button>
					    <ul class="dropdown-menu"> 
					        @foreach($role as $roles)
							    <li><a href="{{ route('master_users.filter_otoritas',$roles->id) }}">{{ $roles->display_name }}</a></li>
							@endforeach
					    </ul>
					  </div>  
					</div>
				</div>

					<div class="table-responsive">
					{!! $html->table(['class'=>'table-striped table']) !!}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
{!! $html->scripts() !!}
<script type="text/javascript">
	// confirm delete
		$(document.body).on('submit', '.js-confirm', function () {
		var $el = $(this)
		var text = $el.data('confirm') ? $el.data('confirm') : 'Anda yakin melakukan tindakan ini\
	?'
		var c = confirm(text);
		return c;
	}); 
</script>
@endsection
