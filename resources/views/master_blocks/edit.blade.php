@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="breadcrumb">
					<li><a href="{{ url('/home') }} ">Home</a></li>
					<li><a href="{{ url('/admin/master_blocks') }}">Block</a></li>
					<li class="active">Edit Block</li>
				</ul>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h2 class="panel-title">Edit Block</h2>
					</div>

					<div class="panel-body">
						{!! Form::model($master_blocks, ['url' => route('master_blocks.update', $master_blocks->id), 'method' => 'put', 'files'=>'true','class'=>'form-horizontal']) !!}
						@include('master_blocks._form')
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
	  items: [<?php echo  $data_pj_dosen; ?>]
	});
</script>
@endsection