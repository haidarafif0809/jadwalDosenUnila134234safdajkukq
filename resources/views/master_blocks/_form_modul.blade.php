<div class="form-group{{ $errors->has('id_modul') ? ' has-error' : '' }}">
	{!! Form::label('id_modul', 'Modul', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::select('id_modul', []+App\Modul::pluck('nama_modul','id')->all(), null, ['class'=>'form-control js-selectize-reguler', 'placeholder' => 'Pilih Modul']) !!}
		{!! $errors->first('id_modul', '<p class="help-block">:message</p>') !!}
	</div>
</div>


<div class="form-group{{ $errors->has('dari_tanggal') ? ' has-error' : '' }}">
	{!! Form::label('dari_tanggal', 'Dari Tanggal', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('dari_tanggal', null, ['class'=>'form-control datepicker','required','autocomplete'=>'off']) !!}
		{!! $errors->first('dari_tanggal', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="form-group{{ $errors->has('sampai_tanggal') ? ' has-error' : '' }}">
	{!! Form::label('sampai_tanggal', 'Sampai Tanggal', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('sampai_tanggal', null, ['class'=>'form-control datepicker','required','autocomplete'=>'off']) !!}
		{!! $errors->first('sampai_tanggal', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="form-group">
	<div class="col-md-4 col-md-offset-2">
		{!! Form::submit('Simpan', ['class'=>'btn btn-primary']) !!}
	</div>
</div>