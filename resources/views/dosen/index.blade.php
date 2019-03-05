@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<ul class="breadcrumb">
				<li><a href="{{ url('/home') }} ">Home</a></li>
			</ul>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Jadwal Saya</h2>
				</div>

				<div class="panel-body">
				<p>
					{!! Form::open(['url' => route('home'),'method' => 'get', 'class'=>'form-inline']) !!}
						@include('dosen._form')
					{!! Form::close() !!}
					</p>

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