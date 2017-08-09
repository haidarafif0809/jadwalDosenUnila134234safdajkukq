@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="breadcrumb">
					<li><a href="{{ url('/home') }} ">Home</a></li>
					<li><a href="{{ url('/admin/penjadwalans') }}">Penjadwalan</a></li>
					<li class="active">Edit Penjadwalan</li>
				</ul>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h2 class="panel-title">Edit Penjadwalan</h2>
					</div>

					<div class="panel-body">  
						{!! Form::model($penjadwalans, ['url' => route('penjadwalans.update', $penjadwalans->id), 'method' => 'put', 'files'=>'true','class'=>'form-horizontal']) !!}
						@include('penjadwalans._form')
						{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
	 
@section('scripts')
<script type="text/javascript">
	$('.js-selectize-multi-edit').selectize({
	  sortField: 'text',
	  delimiter: ',',
	  maxItems: null,
	  items: ['1','2']
	});
</script>
@endsection