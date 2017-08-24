<div class="form-group{{ $errors->has('kode_ruangan') ? ' has-error' : '' }}">
	{!! Form::label('kode_ruangan', 'Kode Ruangan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('kode_ruangan', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('kode_ruangan', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="form-group{{ $errors->has('nama_ruangan') ? ' has-error' : '' }}">
	{!! Form::label('nama_ruangan', 'Nama Ruangan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('nama_ruangan', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('nama_ruangan', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="form-group{{ $errors->has('lokasi_ruangan') ? ' has-error' : '' }}">
	{!! Form::label('lokasi_ruangan', 'Lokasi Ruangan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('lokasi_ruangan', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('lokasi_ruangan', '<p class="help-block">:message</p>') !!}
	</div>
</div> 

<div class="form-group{{ $errors->has('longitude') ? ' has-error' : '' }}">
	{!! Form::label('longitude', 'Longitude', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('longitude', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('longitude', '<p class="help-block">:message</p>') !!}
	</div>
</div> 

<div class="form-group{{ $errors->has('latitude') ? ' has-error' : '' }}">
	{!! Form::label('latitude', 'Latitude', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('latitude', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('latitude', '<p class="help-block">:message</p>') !!}
	</div>
</div> 

<div class="form-group{{ $errors->has('batas_jarak_absen') ? ' has-error' : '' }}">
	{!! Form::label('batas_jarak_absen', 'Batas Jarak Absen', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('batas_jarak_absen', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('batas_jarak_absen', '<p class="help-block">:message</p>') !!}
	</div>
</div> 

<div class="form-group">
	<div class="col-md-4 col-md-offset-2">
		{!! Form::submit('Simpan', ['class'=>'btn btn-primary']) !!}
	</div>
</div>
