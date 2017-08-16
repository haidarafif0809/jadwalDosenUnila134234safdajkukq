
<div class="form-group{{ $errors->has('id_block') ? ' has-error' : '' }}">
		{!! Form::select('id_block', ['semua' => 'Semua Block'	]+App\Master_block::pluck('nama_block','id')->all(), $value = 'semua', ['class'=>'form-control js-selectize-reguler', 'placeholder' => 'Pilih Block']) !!}
		{!! $errors->first('id_block', '<p class="help-block">:message</p>') !!}
	
</div>


<div class="form-group{{ $errors->has('id_ruangan') ? ' has-error' : '' }}">
		{!! Form::select('id_ruangan', ['semua' => 'Semua Ruangan'	]+App\Master_ruangan::pluck('nama_ruangan','id')->all(), $value = 'semua', ['class'=>'form-control js-selectize-reguler', 'placeholder' => 'Pilih Ruangan']) !!}
		{!! $errors->first('id_ruangan', '<p class="help-block">:message</p>') !!}
	
</div>

<div class="form-group{{ $errors->has('id_dosen') ? ' has-error' : '' }}">
	
		{!! Form::select('id_dosen',$users, $value = 'semua', ['class'=>'form-control js-selectize-reguler ', 'placeholder' => 'Pilih Dosen']) !!}
	
		{!! $errors->first('id_dosen', '<p class="help-block">:message</p>') !!}
	
</div>

<div class="form-group{{ $errors->has('dari_tanggal') ? ' has-error' : '' }}">
	
		{!! Form::text('dari_tanggal', null, ['class'=>'form-control datepicker','required','autocomplete'=>'off','placeholder' => 'Dari Tanggal']) !!}
		{!! $errors->first('dari_tanggal', '<p class="help-block">:message</p>') !!}
	
</div>


<div class="form-group{{ $errors->has('sampai_tanggal') ? ' has-error' : '' }}">
	
		{!! Form::text('sampai_tanggal', null, ['class'=>'form-control datepicker','required','autocomplete'=>'off','placeholder' => 'Sampai Tanggal']) !!}
		{!! $errors->first('sampai_tanggal', '<p class="help-block">:message</p>') !!}
	
</div>


<div class="form-group">
	
		{!! Form::submit('Terapkan Filter', ['class'=>'btn btn-primary']) !!}
	
</div>