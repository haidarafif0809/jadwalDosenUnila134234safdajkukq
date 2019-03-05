<div class="form-group{{ $errors->has('modul') ? ' has-error' : '' }}">
	{!! Form::label('modul', 'Modul', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::select('modul', []+App\Modul::pluck('nama_modul','id')->all(), null, ['class'=>'form-control js-selectize-reguler', 'placeholder' => 'Pilih Modul']) !!}
		{!! $errors->first('modul', '<p class="help-block">:message</p>') !!}
	</div>
</div>


<div class="form-group{{ $errors->has('dari_tanggal') ? ' has-error' : '' }}">
	{!! Form::label('dari_tanggal', 'Tanggal Mulai', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('dari_tanggal', null, ['class'=>'form-control datepicker-modul','required','autocomplete'=>'off','readonly' => '']) !!}
		{!! $errors->first('dari_tanggal', '<p class="help-block">:message</p>') !!}
	</div>
</div>



<div class="form-group">
	<div class="col-md-4 col-md-offset-2">
		{!! Form::submit('Simpan', ['class'=>'btn btn-primary']) !!}
	</div>
</div>