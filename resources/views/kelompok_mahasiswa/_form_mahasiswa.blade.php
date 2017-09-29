<div class="form-group{{ $errors->has('mahasiswa') ? ' has-error' : '' }}">
	{!! Form::label('mahasiswa', 'Mahasiswa', ['class'=>'col-md-2 control-label']) !!}
<div class="col-md-4">
		{!! Form::select('mahasiswa', $mahasiswa, null, ['class'=>'form-control js-selectize-reguler', 'placeholder' => 'Pilih Mahasiswa']) !!}
		{!! $errors->first('mahasiswa', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group">
	<div class="col-md-4 col-md-offset-2">
		{!! Form::submit('Simpan', ['class'=>'btn btn-primary']) !!}
	</div>
</div>