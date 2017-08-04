<div class="form-group{{ $errors->has('tanggal') ? ' has-error' : '' }}">
	{!! Form::label('tanggal', 'Tanggal', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::date('tanggal', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('tanggal', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="form-group{{ $errors->has('waktu_mulai') ? ' has-error' : '' }}">
	{!! Form::label('waktu_mulai', 'Mulai', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::time('waktu_mulai', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('waktu_mulai', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="form-group{{ $errors->has('waktu_selesai') ? ' has-error' : '' }}">
	{!! Form::label('waktu_selesai', 'Selesai', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::time('waktu_selesai', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('waktu_selesai', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="form-group{{ $errors->has('id_block') ? ' has-error' : '' }}">
	{!! Form::label('id_block', 'Block', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::select('id_block', []+App\Master_block::pluck('nama_block','id')->all(), null, ['class'=>'form-control js-selectize', 'placeholder' => 'Pilih Block']) !!}
		{!! $errors->first('id_block', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="form-group{{ $errors->has('id_mata_kuliah') ? ' has-error' : '' }}">
	{!! Form::label('id_mata_kuliah', 'Mata Kuliah', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::select('id_mata_kuliah', []+App\Master_mata_kuliah::pluck('nama_mata_kuliah','id')->all(), null, ['class'=>'form-control js-selectize', 'placeholder' => 'Pilih Mata Kuliah']) !!}
		{!! $errors->first('id_mata_kuliah', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="form-group{{ $errors->has('id_ruangan') ? ' has-error' : '' }}">
	{!! Form::label('id_ruangan', 'Ruangan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::select('id_ruangan', []+App\Master_ruangan::pluck('nama_ruangan','id')->all(), null, ['class'=>'form-control js-selectize', 'placeholder' => 'Pilih Ruangan']) !!}
		{!! $errors->first('id_ruangan', '<p class="help-block">:message</p>') !!}
	</div>
</div>