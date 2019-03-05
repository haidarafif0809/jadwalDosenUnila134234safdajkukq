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
					<p> <a class="btn btn-primary" href="{{ route('penjadwalans.create') }}">Tambah Penjadwalan </a>

					 <button data-toggle="collapse" data-target="#filter" class="btn btn-primary"> <span class="glyphicon glyphicon-filter"></span> Filter</button>


					<div id="filter" class="collapse">
					
					{!! Form::open(['url' => route('penjadwalans.store'),'method' => 'post', 'class'=>'form-inline']) !!}
					@include('penjadwalans._form_filter')
					{!! Form::close() !!}
					</div>





					 </p>
					<br>
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
