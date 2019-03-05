<div class="form-group{{ $errors->has('kode_modul') ? ' has-error' : '' }}">
	{!! Form::label('kode_modul', 'Kode Modul', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('kode_modul', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('kode_modul', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="form-group{{ $errors->has('nama_modul') ? ' has-error' : '' }}">
	{!! Form::label('nama_modul', 'Nama Modul', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('nama_modul', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('nama_modul', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="form-group">
	<div class="col-md-4 col-md-offset-2">
		{!! Form::submit('Simpan', ['class'=>'btn btn-primary']) !!}
	</div>
</div>