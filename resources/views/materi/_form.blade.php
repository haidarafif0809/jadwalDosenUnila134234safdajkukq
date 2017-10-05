<div class="form-group{{ $errors->has('nama_materi') ? ' has-error' : '' }}">
	{!! Form::label('nama_materi', 'Nama Materi', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('nama_materi', null, ['class'=>'form-control','required','autocomplete'=>'off', 'id' => 'nama_materi']) !!}
		{!! $errors->first('nama_materi', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="form-group">
	<div class="col-md-4 col-md-offset-2">
		{!! Form::submit('Simpan', ['class'=>'btn btn-primary']) !!}
	</div>
</div>