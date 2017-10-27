@extends('layouts.app')
@section('content')
<style type="text/css">
	ul { list-style: none; }
	li { padding: 5px 0; }


</style>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ul class="breadcrumb">
				<li><a href="{{ url('/home') }}">Home</a></li>
				<li><a href="{{ route('master_users.index') }}">User</a></li>
				<li class="active">Info User</li>
			</ul>
 
			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Info User</h2>
				</div>

				<div class="panel-body"> 
	
					<p>
						<ul>
						<li>Nama : {{ $user->name }} </li>
						<li>Username / NIP/ NPM : {{$user->email}} </li>
						<li>No Hp : {{$user->no_hp}} </li>
						<li>Alamat: {{$user->alamat}} </li>
						<li>Otoritas: {{$user->role->role->display_name}} </li>
						@if($user->angkatan != NULL)
						<li>Angkatan : {{$user->angkatan->nama_angkatan}}</li>
						@endif
						@if($kelompok_mahasiswa->count() > 0)
						 <li>Kelompok Tutor / CSL : 
						 	@foreach($kelompok_mahasiswa->get() as $kelompok)
							 	@if ($loop->first)
							        {{$kelompok->kelompok->nama_kelompok_mahasiswa}}
							    @else 
							    , {{$kelompok->kelompok->nama_kelompok_mahasiswa}}
							    @endif
						 	@endforeach
						</li>
						@endif
						</ul>
					</p>
		
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')

@endsection
