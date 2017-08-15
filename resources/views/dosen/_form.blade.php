<div class="form-group{{ $errors->has('dari_tanggal') ? ' has-error' : '' }}">
	
		{!! Form::text('dari_tanggal', null, ['class'=>'form-control datepicker','required','autocomplete'=>'off','placeholder' => 'Dari Tanggal']) !!}
		{!! $errors->first('dari_tanggal', '<p class="help-block">:message</p>') !!}

</div>
<div class="form-group{{ $errors->has('sampai_tanggal') ? ' has-error' : '' }}">
		{!! Form::text('sampai_tanggal', null, ['class'=>'form-control datepicker','required','autocomplete'=>'off','placeholder' => 'Sampai Tanggal']) !!}
		{!! $errors->first('sampai_tanggal', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
	
		{!! Form::submit('Simpan', ['class'=>'btn btn-primary']) !!}
	
</div>