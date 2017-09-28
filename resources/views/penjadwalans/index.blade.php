@extends('layouts.app')
@section('content')

<style type="text/css">
	#filter {
		margin-top: 10px;
	}
</style>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ul class="breadcrumb">
				<li><a href="{{ url('/home') }}">Home</a></li>
				<li class="active">Penjadwalan</li>
			</ul>
 
			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Penjadwalan</h2>
				</div>

				<div class="panel-body">
					<p>

				<!-- APABILA ADMIN DAN PJ DOSEN YANG LOGIN MAKA MUNCUL TOMBOL DI BAWAH-->
					@role('admin') <a class="btn btn-primary" href="{{ route('penjadwalans.create') }}"><span class="glyphicon glyphicon-plus"></span> Tambah Penjadwalan </a> @endrole
					@role('pj_dosen') <a class="btn btn-primary" href="{{ route('penjadwalans.create') }}"><span class="glyphicon glyphicon-plus"></span> Tambah Penjadwalan </a> @endrole
					@role('dosen') <a class="btn btn-primary" href="{{ route('penjadwalans.create') }}"><span class="glyphicon glyphicon-plus"></span> Tambah Penjadwalan </a> @endrole
				<!-- //APABILA ADMIN DAN PJ DOSEN YANG LOGIN MAKA MUNCUL TOMBOL DI BAWAH-->

				<!-- MEMBUAT FILTER PENJADWALAN -->
					 <button data-toggle="collapse" data-target="#filter" id="button_filter" class="btn btn-primary"> <span class="glyphicon glyphicon-filter"></span> Filter</button> 
					 <a class="btn btn-primary" href="{{ route('penjadwalans.index') }}"> <span class="glyphicon glyphicon-remove"></span>  Hapus Filter</a> 
				<!-- //MEMBUAT FILTER PENJADWALAN -->

				<!-- MEMBUAT TOMBOL JADWAL PER BLOCK-->
					 <a class="btn btn-primary" href="{{ route('master_blocks.index'	) }}"><span class="glyphicon glyphicon-th-list"></span> Jadwal Per Block </a>
				<!-- //MEMBUAT TOMBOL JADWAL PER BLOCK-->

				<!-- MEMBUAT TOMBOL EXPORT EXCEL -->
					  <button data-toggle="collapse" data-target="#export" id="button_export" class="btn btn-primary"> <span class="glyphicon glyphicon-export"></span> Export Excel</button> 
				<!-- //MEMBUAT TOMBOL EXPORT EXCEL -->
					  
				<!-- MEMBUAT TOMBOL FILTER PENJADWALAN -->
					<div id="filter" style="display:none;"> 
					{!! Form::open(['url' => route('penjadwalans.filter'),'method' => 'get', 'class'=>'form-inline']) !!}
					@include('penjadwalans._form_filter')
					{!! Form::close() !!}
					</div>
				<!-- //MEMBUAT TOMBOL FILTER PENJADWALAN -->
  
				<!-- MEMBUAT FILTER EXPORT PENJADWALAN -->
					<div id="export" style="display:none;">
					{!! Form::open(['url' => route('penjadwalans.export'),'method' => 'post', 'class'=>'form-inline']) !!}
					@include('penjadwalans._form_export')
					{!! Form::close() !!}
					</div> 
				<!-- //MEMBUAT FILTER EXPORT PENJADWALAN -->

					 </p>
					<br>

				<!-- MENAMPILKAN DATA PENJADWALAN -->
					<div class="table-responsive">
					{!! $html->table(['class'=>'table-striped table']) !!}
					</div>
				<!-- //MENAMPILKAN DATA PENJADWALAN -->
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


<script type="text/javascript">
$('.datepicker_filter').datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
});
</script>

<script type="text/javascript">
$(document.body).on('click', '#button_filter', function () {
	$("#filter").show();
	$("#export").hide();
	});
$(document.body).on('click', '#button_export', function () {
	$("#export").show();
	$("#filter").hide();
	});  
</script>

<script type="text/javascript">
	$(document.body).on('click', '.btn-terlaksana', function () {
	 var id =  $(this).attr('data-id');
	 $('.form-terlaksana-'+id).submit();
 
		});	
</script>

<script type="text/javascript">
	$(document.body).on('click', '.btn-batal-jadwal', function () {
	 var id =  $(this).attr('data-id');
	 $('.form-batal-jadwal-'+id).submit();
 
		});	
</script>
@endsection
