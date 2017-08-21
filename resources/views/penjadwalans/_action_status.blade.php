
@if($model->status_jadwal == 0)
@role('admin') 
	{!! Form::model($model, ['url' => $terlaksana_url, 'method' => 'get', 'class' => 'form-inline js-confirm', 'data-confirm' => $terlaksana_message]) !!}  
	{!! Form::submit('Terlaksana', ['class'=>'btn btn-info  btn-sm']) !!}
	{!! Form::close()!!}
	{!! Form::model($model, ['url' => $batal_url, 'method' => 'get', 'class' => 'form-inline js-confirm', 'data-confirm' => $batal_message]) !!}  

	{!! Form::submit('Batal', ['class'=>'btn btn-danger  btn-sm']) !!} 
	{!! Form::close()!!}	
@endrole	
  @role('dosen') 
  @if(isset($asal_input) && $model->jadwal->status_jadwal == 0)
	{!! Form::model($model, ['url' => $batal_url, 'method' => 'get', 'class' => 'form-inline js-confirm', 'data-confirm' => $batal_message]) !!}  
	{!! Form::hidden('asal_input', $asal_input, null,null) !!}
	{!! Form::hidden('id_jadwal', $model->id_jadwal, null,null) !!}
	{!! Form::submit('Batal', ['class'=>'btn btn-danger  btn-sm']) !!} 
	{!! Form::close()!!}	

	@endif
 @endrole
@elseif($model->status_jadwal == 1 )     

 @role('admin') 
	{!! Form::model($model, ['url' => $belum_terlaksana_url, 'method' => 'get', 'class' => 'form-inline js-confirm', 'data-confirm' => $belum_terlaksana_message]) !!}  
	{!! Form::submit('Belum Terlaksana', ['class'=>'btn btn-primary  btn-sm']) !!} 
	{!! Form::close()!!}
  @endrole
@endif
