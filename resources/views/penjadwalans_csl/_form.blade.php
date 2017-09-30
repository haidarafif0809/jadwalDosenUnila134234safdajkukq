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
<!-- //APABILA DOSEN YANG LOGIN MAKA INPUT DI BAWAH MUNCUL -->

<!-- APABILA DOSEN YANG LOGIN MAKA INPUT DI BAWAH MUNCUL -->
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


<div class="form-group{{ $errors->has('id_materi') ? ' has-error' : '' }}">
	{!! Form::label('id_materi', 'Materi', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::select('id_materi', []+App\Materi::pluck('nama_materi','id')->all(), null, ['class'=>'form-control js-selectize-reguler', 'placeholder' => 'Pilih Materi']) !!}
		{!! $errors->first('id_materi', '<p class="help-block">:message</p>') !!}
	</div>
</div>


<!--TANGGAL INPUTAN 1-->
@if(isset($asal_input))
<div class="form-group{{ $errors->has('tanggal') ? ' has-error' : '' }}">
	{!! Form::label('tanggal', 'Tanggal Pertemuan 1', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('tanggal', null, ['class'=>'form-control datepicker-jadwal','required','autocomplete'=>'off','readonly' => '']) !!}
		{!! $errors->first('tanggal', '<p class="help-block">:message</p>') !!}
	</div>
</div>
@else

<div class="form-group{{ $errors->has('tanggal') ? ' has-error' : '' }}">
	{!! Form::label('tanggal', 'Tanggal Pertemuan 1', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('tanggal', null, ['class'=>'form-control datepicker-jadwal','required','autocomplete'=>'off','readonly' => '', 'placeholder' => 'Pilih Tanggal']) !!}
		{!! $errors->first('tanggal', '<p class="help-block">:message</p>') !!}
	</div>
</div>
@endif

@if(isset($data_waktu))
<div class="form-group{{ $errors->has('data_waktu') ? ' has-error' : '' }}">
	{!! Form::label('data_waktu', 'Waktu Pertemuan 1', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4"> 
		{!! Form::select('data_waktu', []+App\SettingWaktu::select([DB::raw('CONCAT(waktu_mulai, " - ", waktu_selesai) AS waktu')])->pluck('waktu','waktu')->all(), $value = $data_waktu, ['class'=>'form-control js-selectize-reguler ', 'placeholder' => 'Pilih Waktu']) !!} 
		{!! $errors->first('data_waktu', '<p class="help-block">:message</p>') !!}
	</div>
</div>
@else
<div class="form-group{{ $errors->has('data_waktu') ? ' has-error' : '' }}">
	{!! Form::label('data_waktu', 'Waktu Pertemuan 1', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4"> 
		{!! Form::select('data_waktu', []+App\SettingWaktu::select([DB::raw('CONCAT(waktu_mulai, " - ", waktu_selesai) AS waktu')])->pluck('waktu','waktu')->all(), null, ['class'=>'form-control js-selectize-reguler ', 'placeholder' => 'Pilih Waktu']) !!} 
		{!! $errors->first('data_waktu', '<p class="help-block">:message</p>') !!}
	</div>
</div>

@endif
<!--TANGGAL INPUTAN 1-->

<!--TANGGAL INPUTAN 2-->

@if(isset($asal_input))
<div class="form-group{{ $errors->has('tanggal_2') ? ' has-error' : '' }}">
	{!! Form::label('tanggal_2', 'Tanggal Pertemuan 2', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('tanggal_2', null, ['class'=>'form-control datepicker-jadwal','required','autocomplete'=>'off','readonly' => '']) !!}
		{!! $errors->first('tanggal_2', '<p class="help-block">:message</p>') !!}
	</div>
</div>
@else

<div class="form-group{{ $errors->has('tanggal_2') ? ' has-error' : '' }}">
	{!! Form::label('tanggal_2', 'Tanggal Pertemuan 2', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('tanggal_2', null, ['class'=>'form-control datepicker-jadwal','required','autocomplete'=>'off','readonly' => '', 'placeholder' => 'Pilih Tanggal']) !!}
		{!! $errors->first('tanggal_2', '<p class="help-block">:message</p>') !!}
	</div>
</div>
@endif

@if(isset($data_waktu))
<div class="form-group{{ $errors->has('data_waktu_2') ? ' has-error' : '' }}">
	{!! Form::label('data_waktu_2', 'Waktu Pertemuan 2', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4"> 
		{!! Form::select('data_waktu_2', []+App\SettingWaktu::select([DB::raw('CONCAT(waktu_mulai, " - ", waktu_selesai) AS waktu')])->pluck('waktu','waktu')->all(), $value = $data_waktu, ['class'=>'form-control js-selectize-reguler ', 'placeholder' => 'Pilih Waktu']) !!} 
		{!! $errors->first('data_waktu_2', '<p class="help-block">:message</p>') !!}
	</div>
</div>
@else
<div class="form-group{{ $errors->has('data_waktu_2') ? ' has-error' : '' }}">
	{!! Form::label('data_waktu_2', 'Waktu Pertemuan 2', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4"> 
		{!! Form::select('data_waktu_2', []+App\SettingWaktu::select([DB::raw('CONCAT(waktu_mulai, " - ", waktu_selesai) AS waktu')])->pluck('waktu','waktu')->all(), null, ['class'=>'form-control js-selectize-reguler ', 'placeholder' => 'Pilih Waktu']) !!} 
		{!! $errors->first('data_waktu_2', '<p class="help-block">:message</p>') !!}
	</div>
</div>

@endif
<!--TANGGAL INPUTAN 2-->


<div class="form-group{{ $errors->has('id_ruangan') ? ' has-error' : '' }}">
	{!! Form::label('id_ruangan', 'Ruangan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::select('id_ruangan', []+App\Master_ruangan::pluck('nama_ruangan','id')->all(), null, ['class'=>'form-control js-selectize-reguler', 'placeholder' => 'Pilih Ruangan']) !!}
		{!! $errors->first('id_ruangan', '<p class="help-block">:message</p>') !!}
	</div>
</div>



<div class="form-group{{ $errors->has('id_user[]') ? ' has-error' : '' }}">
	{!! Form::label('id_user[]', 'Dosen', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
	@if (isset($penjadwalans) && $penjadwalans)  
		{!! Form::select('id_user[]', $users, null, ['class'=>'form-control js-selectize-multi-edit ', 'placeholder' => 'Pilih Dosen' ,'required' => 'true']) !!}
	@else
		{!! Form::select('id_user[]', $users, null, ['class'=>'form-control js-selectize-multi ', 'placeholder' => 'Pilih Dosen','required' => 'true']) !!}
	@endif
		{!! $errors->first('id_user[]', '<p class="help-block">:message</p>') !!}
	</div>
</div>


<div class="form-group{{ $errors->has('id_kelompok') ? ' has-error' : '' }}">
	{!! Form::label('id_kelompok', 'Kelompok Mahasiswa', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::select('id_kelompok', $kelompoks , null, ['class'=>'form-control js-selectize-reguler', 'placeholder' => 'Pilih Kelompok']) !!}
		{!! $errors->first('id_kelompok', '<p class="help-block">:message</p>') !!}
	</div>
</div>


<div class="form-group">
	<div class="col-md-4 col-md-offset-2">
		{!! Form::submit('Simpan', ['class'=>'btn btn-primary']) !!}
	</div>
</div>