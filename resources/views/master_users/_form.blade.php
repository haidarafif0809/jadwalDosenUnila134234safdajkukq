<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
	{!! Form::label('name', 'Nama', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('name', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('name', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
	{!! Form::label('email', 'Username', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('email', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('email', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="form-group{{ $errors->has('no_hp') ? ' has-error' : '' }}">
	{!! Form::label('no_hp', 'Nomor Hp', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('no_hp', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('no_hp', '<p class="help-block">:message</p>') !!}
	</div>
</div> 

<div class="form-group{{ $errors->has('alamat') ? ' has-error' : '' }}">
	{!! Form::label('alamat', 'Alamat', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::text('alamat', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
		{!! $errors->first('alamat', '<p class="help-block">:message</p>') !!}
	</div>
</div>

 

@if (isset($master_users) && $master_users)  
<div class="form-group{{ $errors->has('role_id[]') ? ' has-error' : '' }}">
	{!! Form::label('role_id[]', 'Otoritas', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::select('role_id[]', []+App\Role::pluck('display_name','id')->all(), null, ['class'=>'form-control js-selectize-multi-edit role-id', 'placeholder' => 'Pilih Otoritas']) !!}
		{!! $errors->first('role_id[]', '<p class="help-block">:message</p>') !!}
	</div>
</div> 
@else
<div class="form-group{{ $errors->has('role_id[]') ? ' has-error' : '' }}">
	{!! Form::label('role_id[]', 'Otoritas', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::select('role_id[]', []+App\Role::pluck('display_name','id')->all(), null, ['class'=>'form-control js-selectize-multi role-id', 'placeholder' => 'Pilih Otoritas']) !!}
		{!! $errors->first('role_id[]', '<p class="help-block">:message</p>') !!}
	</div>
</div>
@endif
<div class="form-group{{ $errors->has('id_angkatan') ? ' has-error' : '' }}" style="display: none;" id="data_angkatan">
	{!! Form::label('id_angkatan', 'Angkatan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-4">
		{!! Form::select('id_angkatan', []+App\Angkatan::pluck('nama_angkatan','id')->all(), null, ['class'=>'form-control js-selectize-reguler', 'placeholder' => 'Pilih Angkatan','id'=>'id_angkatan']) !!}
		{!! $errors->first('id_angkatan', '<p class="help-block">:message</p>') !!}
	</div>
</div> 


<div class="form-group">
	<div class="col-md-4 col-md-offset-2">
		{!! Form::submit('Simpan', ['class'=>'btn btn-primary']) !!}
	</div>
</div>
