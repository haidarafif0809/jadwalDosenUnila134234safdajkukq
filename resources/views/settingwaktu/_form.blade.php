<div class="form-group{{ $errors->has('waktu_mulai') ? ' has-error' : '' }}">
	{!! Form::label('waktu_mulai', 'Waktu Mulai', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::time('waktu_mulai', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('waktu_mulai', '<p class="help-block">Mohon Maaf Waktu Mulai Yang Anda Isi Sudah Ada</p>') !!}
	</div>  
</div>

<div class="form-group{{ $errors->has('waktu_selesai') ? ' has-error' : '' }}">
	{!! Form::label('waktu_selesai', 'Waktu Selesai', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::time('waktu_selesai', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('waktu_selesai', '<p class="help-block">Mohon Maaf Waktu Selesai Yang Anda Isi Sudah Ada</p>') !!}
	</div>  
</div>

<div class="form-group">
	<div class="col-md-4 col-md-offset-2">
		{!! Form::submit('Simpan', ['class'=>'btn btn-primary']) !!}
	</div>
</div>