<div class="form-group{{ $errors->has('kode_mata_kuliah') ? ' has-error' : '' }}">
	{!! Form::label('kode_mata_kuliah', 'Kode Mata Kuliah', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('kode_mata_kuliah', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('kode_mata_kuliah', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="form-group{{ $errors->has('nama_mata_kuliah') ? ' has-error' : '' }}">
	{!! Form::label('nama_mata_kuliah', 'Nama Mata Kuliah', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('nama_mata_kuliah', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('nama_mata_kuliah', '<p class="help-block">:message</p>') !!}
	</div>
</div>
 
<div class="form-group">
	<div class="col-md-4 col-md-offset-2">
		{!! Form::submit('Simpan', ['class'=>'btn btn-primary']) !!}
	</div>
</div>
