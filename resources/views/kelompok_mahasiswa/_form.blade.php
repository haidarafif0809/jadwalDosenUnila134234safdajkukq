<div class="form-group{{ $errors->has('nama_kelompok_mahasiswa') ? ' has-error' : '' }}">
	{!! Form::label('nama_kelompok_mahasiswa', 'Nama Kelompok Mahasiswa', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('nama_kelompok_mahasiswa', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('nama_kelompok_mahasiswa', '<p class="help-block">:message</p>') !!}
	</div>
</div>
 
<div class="form-group{{ $errors->has('id_angkatan') ? ' has-error' : '' }}">
	{!! Form::label('id_angkatan', 'Angkatan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4"> 
		{!! Form::select('id_angkatan', []+App\Angkatan::pluck('nama_angkatan','id')->all(), null, ['class'=>'form-control js-selectize-reguler ', 'placeholder' => 'Pilih Angkatan']) !!} 
		{!! $errors->first('id_angkatan', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="form-group">
	<div class="col-md-4 col-md-offset-2">
		{!! Form::submit('Simpan', ['class'=>'btn btn-primary']) !!}
	</div>
</div>