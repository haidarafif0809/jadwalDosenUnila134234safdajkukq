@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ul class="breadcrumb">
				<li><a href="{{ url('/home') }} ">Home</a></li>
				<li><a href="{{ route('penjadwalans.index') }} ">Penjadwalan</a></li>
				<li><a href="{{ route('master_blocks.modul',$modul->id_blok) }} ">Modul</a></li>
				<li class="active">Jadwal Kuliah {{ $modul->block->nama_block }} : {{ $modul->modul->nama_modul}}</li>
			</ul>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Jadwal Kuliah {{ $modul->block->nama_block }} : {{ $modul->modul->nama_modul}}</h2>
				</div>

				<div class="panel-body">
				@role('admin')
				<p> <button data-toggle="collapse" data-target="#form_penjadwalan" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Tambah Jadwal</button>
</p>
				
					<div id="form_penjadwalan" class="collapse">
					{!! Form::open(['url' => route('penjadwalans.store'),'method' => 'post', 'class'=>'form-horizontal']) !!}
										@include('penjadwalans._form')
										{!! Form::close() !!}
					</div>
				@endrole

					@include('mahasiswa.schedule_mahasiswa');
				</div>
			</div>
		</div>
	</div>
</div>




@endsection

@section('scripts')

<script type="text/javascript">
	

		$('.single-event').click(function(){
			var id_jadwal = $(this).attr('data-id');
			$.post('{{ route('jadwal.info')}}',{

			 '_token': $('meta[name=csrf-token]').attr('content'),
			 'id_jadwal' :id_jadwal
			},function(data){
				$('.isi-event').html(data);
			});
			
		});
	
</script>
@endsection