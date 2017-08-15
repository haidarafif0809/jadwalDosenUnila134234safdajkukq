
<div class="form-group{{ $errors->has('block') ? ' has-error' : '' }}">
	{!! Form::label('block', 'Block', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::select('block', $block, null, ['class'=>'form-control js-selectize-reguler', 'placeholder' => 'Pilih block']) !!}
		{!! $errors->first('block', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group{{ $errors->has('modul') ? ' has-error' : '' }}">
	{!! Form::label('modul', 'Modul', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::select('modul', [], null, ['class'=>'form-control js-selectize-reguler', 'placeholder' => 'Pilih Modul']) !!}
		{!! $errors->first('modul', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="form-group">
	<div class="col-md-4 col-md-offset-2">
		{!! Form::submit('Lihat', ['class'=>'btn btn-primary']) !!}
	</div>
</div>



