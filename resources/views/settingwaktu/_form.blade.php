<div class="form-group{{ $errors->has('waktu') ? ' has-error' : '' }}">
	{!! Form::label('waktu', 'Waktu', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::time('waktu', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('waktu', '<p class="help-block">Mohon Maaf Waktu Yang Anda Isi Sudah Ada</p>') !!}
	</div> 
		{!! Form::submit('Simpan', ['class'=>'btn btn-primary']) !!}
	</div>
</div>
