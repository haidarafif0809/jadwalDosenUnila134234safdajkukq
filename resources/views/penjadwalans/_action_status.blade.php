@role('admin') 
@if($model->status_jadwal == 0)
	{!! Form::model($model, ['url' => $terlaksana_url, 'method' => 'get', 'class' => 'form-inline js-confirm', 'data-confirm' => $terlaksana_message]) !!}  
	{!! Form::submit('Terlaksana', ['class'=>'btn btn-info  btn-sm']) !!}
	{!! Form::close()!!}
 
	{!! Form::model($model, ['url' => $batal_url, 'method' => 'get', 'class' => 'form-inline js-confirm', 'data-confirm' => $batal_message]) !!}  
	{!! Form::submit('Batal', ['class'=>'btn btn-danger  btn-sm']) !!} 
	{!! Form::close()!!}	

@elseif($model->status_jadwal == 1 )        
	{!! Form::model($model, ['url' => $belum_terlaksana_url, 'method' => 'get', 'class' => 'form-inline js-confirm', 'data-confirm' => $belum_terlaksana_message]) !!}  
	{!! Form::submit('Belum Terlaksana', ['class'=>'btn btn-primary  btn-sm']) !!} 
	{!! Form::close()!!}
  
@endif
@endrole