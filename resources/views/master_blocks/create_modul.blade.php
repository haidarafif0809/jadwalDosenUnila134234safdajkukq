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
			@role('admin')
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
			@endrole
			<!-- panel form  -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">Data Modul Terkait</h2>
				</div>

				<div class="panel-body">
							<div class="table-responsive">
					{!! $html->table(['class'=>'table-striped table']) !!}
					</div>
				</div>
			</div>
			<!-- panel data  -->
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

		
	$('.datepicker-modul').datepicker({
	    format: 'yyyy-mm-dd',
	    daysOfWeekDisabled: '0,6',
	    daysOfWeekHighlighted: '1',
	    daysOfWeekDisabled: "0,2,3,4,5,6",
	    autoclose: true,
	});

</script>
@endsection