@if(isset($asal_input)) 
	{!! Form::hidden('asal_input', $value = 1, ['class'=>'','required','autocomplete'=>'off']) !!} 
@endif 
@if(isset($asal_input)) 
	{!! Form::hidden('id_block', $value = $modul->id_blok, ['class'=>'','required','autocomplete'=>'off']) !!}
	{!! Form::hidden('modul', $value = $modul->id_modul_blok, ['class'=>'','required','autocomplete'=>'off']) !!} 
@else 

<!-- APABILA ADMIN YANG LOGIN MAKA INPUT DI BAWAH MUNCUL -->
@role('admin') 
<div class="form-group{{ $errors->has('id_block') ? ' has-error' : '' }}">
	{!! Form::label('id_block', 'Block', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::select('id_block', []+App\Master_block::pluck('nama_block','id')->all(), null, ['class'=>'form-control js-selectize-reguler', 'placeholder' => 'Pilih Block']) !!}
		{!! $errors->first('id_block', '<p class="help-block">:message</p>') !!}
	</div>
</div>
@endrole 
<!-- //APABILA ADMIN YANG LOGIN MAKA INPUT DI BAWAH MUNCUL -->

<!-- APABILA ADMIN YANG LOGIN MAKA INPUT DI BAWAH MUNCUL -->
@role('dosen') 
<div class="form-group{{ $errors->has('id_block') ? ' has-error' : '' }}">
	{!! Form::label('id_block', 'Block', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::select('id_block', []+App\Master_block::pluck('nama_block','id')->all(), null, ['class'=>'form-control js-selectize-reguler', 'placeholder' => 'Pilih Block']) !!}
		{!! $errors->first('id_block', '<p class="help-block">:message</p>') !!}
	</div>
</div>
@endrole 
<!-- //APABILA ADMIN YANG LOGIN MAKA INPUT DI BAWAH MUNCUL -->

<!-- APABILA PJ DOSEN YANG LOGIN MAKA INPUT DI BAWAH MUNCUL -->
@role('pj_dosen') 
<div class="form-group{{ $errors->has('id_block') ? ' has-error' : '' }}">
	{!! Form::label('id_block', 'Block', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::select('id_block', $data_block, null, ['class'=>'form-control js-selectize-reguler', 'placeholder' => 'Pilih Block']) !!}
		{!! $errors->first('id_block', '<p class="help-block">:message</p>') !!}
	</div>
</div>
@endrole 
<!-- //APABILA PJ DOSEN YANG LOGIN MAKA INPUT DI BAWAH MUNCUL -->


<div class="form-group{{ $errors->has('modul') ? ' has-error' : '' }}">
	{!! Form::label('modul', 'Modul', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
	@if(isset($modul))
	{!! Form::select('modul', $modul, $penjadwalans->id_modul, ['class'=>'form-control js-selectize-reguler', 'data-placeholder' => 'Pilih Modul','required' => 'true']) !!}
	@else 
		{!! Form::select('modul', [], null, ['class'=>'form-control js-selectize-reguler', 'data-placeholder' => 'Pilih Modul','required' => 'true']) !!}

	@endif 
		{!! $errors->first('modul', '<p class="help-block">:message</p>') !!}
	</div>
</div> 

@endif

<div class="form-group{{ $errors->has('tipe_jadwal') ? ' has-error' : '' }}">
	{!! Form::label('tipe_jadwal', 'Tipe Jadwal', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::select('tipe_jadwal', ['KULIAH'=>'KULIAH','PRAKTIKUM'=>'PRAKTIKUM','PLENO'=>'PLENO'], null, ['class'=>'form-control js-selectize-reguler', 'placeholder' => 'Pilih Tipe Jadwal','id' => 'tipe_jadwal']) !!}
		{!! $errors->first('tipe_jadwal', '<p class="help-block">:message</p>') !!}
	</div>
</div>


<div class="form-group{{ $errors->has('id_mata_kuliah') ? ' has-error' : '' }} kolom-mata-kuliah">
	{!! Form::label('id_mata_kuliah', 'Mata Kuliah', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		@if(isset($penjadwalans))
		{!! Form::select('id_mata_kuliah', []+App\Master_mata_kuliah::pluck('nama_mata_kuliah','id')->all(), $penjadwalans->id_mata_kuliah, ['class'=>'form-control js-selectize-reguler','id' => 'id_mata_kuliah', 'placeholder' => 'Pilih Mata Kuliah']) !!}
		@else
		{!! Form::select('id_mata_kuliah', []+App\Master_mata_kuliah::pluck('nama_mata_kuliah','id')->all(), null  , ['class'=>'form-control js-selectize-reguler','id' => 'id_mata_kuliah', 'placeholder' => 'Pilih Mata Kuliah']) !!}
		@endif
		{!! $errors->first('id_mata_kuliah', '<p class="help-block">:message</p>') !!}
	</div>
</div>

@if(isset($penjadwalans))
<div class="form-group{{ $errors->has('tanggal') ? ' has-error' : '' }}">
	{!! Form::label('tanggal', 'Tanggal', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('tanggal', null, ['class'=>'form-control datepicker-modul-jadwal','required','autocomplete'=>'off','readonly' => '']) !!}
		{!! $errors->first('tanggal', '<p class="help-block">:message</p>') !!}
	</div>
</div>
@else

<div class="form-group{{ $errors->has('tanggal') ? ' has-error' : '' }}">
	{!! Form::label('tanggal', 'Tanggal', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('tanggal', null, ['class'=>'form-control datepicker-modul-jadwal','required','autocomplete'=>'off','readonly' => '', 'placeholder' => 'Pilih Tanggal']) !!}
		{!! $errors->first('tanggal', '<p class="help-block">:message</p>') !!}
	</div>
</div>
@endif

@if(isset($data_waktu))
<div class="form-group{{ $errors->has('data_waktu') ? ' has-error' : '' }}">
	{!! Form::label('data_waktu', 'Waktu', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4"> 
		{!! Form::select('data_waktu', []+App\SettingWaktu::select([DB::raw('CONCAT(waktu_mulai, " - ", waktu_selesai) AS waktu')])->pluck('waktu','waktu')->all(), $value = $data_waktu, ['class'=>'form-control js-selectize-reguler ', 'placeholder' => 'Pilih Waktu']) !!} 
		{!! $errors->first('data_waktu', '<p class="help-block">:message</p>') !!}
	</div>
</div>
@else
<div class="form-group{{ $errors->has('data_waktu') ? ' has-error' : '' }}">
	{!! Form::label('data_waktu', 'Waktu', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4"> 
		{!! Form::select('data_waktu', []+App\SettingWaktu::select([DB::raw('CONCAT(waktu_mulai, " - ", waktu_selesai) AS waktu')])->pluck('waktu','waktu')->all(), null, ['class'=>'form-control js-selectize-reguler ', 'placeholder' => 'Pilih Waktu']) !!} 
		{!! $errors->first('data_waktu', '<p class="help-block">:message</p>') !!}
	</div>
</div>

@endif



<div class="form-group{{ $errors->has('id_ruangan[]') ? ' has-error' : '' }}">
	{!! Form::label('id_ruangan[]', 'Ruangan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
			@if (isset($penjadwalans) && $penjadwalans)  
		{!! Form::select('id_ruangan[]', []+App\Master_ruangan::pluck('nama_ruangan','id')->all(), null, ['class'=>'form-control js-selectize-multi-edit-ruangan', 'placeholder' => 'Pilih Ruangan','required'=>'true' ])!!}
		@else
		{!! Form::select('id_ruangan[]', []+App\Master_ruangan::pluck('nama_ruangan','id')->all(), null, ['class'=>'form-control js-selectize-multi', 'placeholder' => 'Pilih Ruangan','required'=>'true' ])!!}
		@endif
		{!! $errors->first('id_ruangan[]', '<p class="help-block">:message</p>') !!}
	</div>
</div>



<div class="form-group{{ $errors->has('id_user[]') ? ' has-error' : '' }}">
	{!! Form::label('id_user[]', 'Dosen', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
	@if (isset($penjadwalans) && $penjadwalans)  
		{!! Form::select('id_user[]', $users, null, ['class'=>'form-control js-selectize-multi-edit', 'placeholder' => 'Pilih Dosen' ,'required' => 'true']) !!}
	@else
		{!! Form::select('id_user[]', $users, null, ['class'=>'form-control js-selectize-multi ', 'placeholder' => 'Pilih Dosen','required' => 'true']) !!}
	@endif
		{!! $errors->first('id_user[]', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="form-group">
	<div class="col-md-4 col-md-offset-2">
		{!! Form::submit('Simpan', ['class'=>'btn btn-primary']) !!}
	</div>
</div>