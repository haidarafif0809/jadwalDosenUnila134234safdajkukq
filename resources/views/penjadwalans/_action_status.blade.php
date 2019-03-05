
@if($model->status_jadwal == 0)



@role('admin') 
<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Konfirmasi
    <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li><a href="#" class="btn-terlaksana" data-id="{{ $model->id }}">Terlaksana</a></li>
      <li><a href="#" class="btn-batal-jadwal" data-id="{{ $model->id }}">Batal</a></li>
      <li><a href="{{ $ubah_dosen }}">Ubah Dosen</a></li> 
    </ul>
</div> 

 {!! Form::model($model, ['url' => $terlaksana_url, 'method' => 'get', 'class' => "form-inline js-confirm form-terlaksana-$model->id", 'data-confirm' => $terlaksana_message]) !!} 
 {!! Form::close()!!}

 {!! Form::model($model, ['url' => $batal_url, 'method' => 'get', 'class' => "form-inline js-confirm form-batal-jadwal-$model->id", 'data-confirm' => $batal_message]) !!}   
 {!! Form::close()!!}	

@endrole	

@role('dosen') 

@if($model_user > 0)


	<div class="dropdown">
	    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Konfirmasi
	    <span class="caret"></span></button>
	    <ul class="dropdown-menu">
	      <li><a href="#" class="btn-terlaksana" data-id="{{ $model->id }}">Terlaksana</a></li>
	      <li><a href="#" class="btn-batal-jadwal" data-id="{{ $model->id }}">Batal</a></li>
	      <li><a href="{{ $ubah_dosen }}">Ubah Dosen</a></li> 
	    </ul>
	</div> 

	 {!! Form::model($model, ['url' => $terlaksana_url, 'method' => 'get', 'class' => "form-inline js-confirm form-terlaksana-$model->id", 'data-confirm' => $terlaksana_message]) !!} 
	 {!! Form::close()!!}

	 {!! Form::model($model, ['url' => $batal_url, 'method' => 'get', 'class' => "form-inline js-confirm form-batal-jadwal-$model->id", 'data-confirm' => $batal_message]) !!}   

@endif
@endrole
   
	@elseif($model->status_jadwal == 1 ) 
		@role('admin') 
			{!! Form::model($model, ['url' => $belum_terlaksana_url, 'method' => 'get', 'class' => 'form-inline js-confirm', 'data-confirm' => $belum_terlaksana_message]) !!}  
			{!! Form::submit('Belum Terlaksana', ['class'=>'btn btn-primary  btn-sm']) !!} 
			{!! Form::close()!!}
		@endrole
		@role('dosen') 
			@if($model_user > 0)
				{!! Form::model($model, ['url' => $belum_terlaksana_url, 'method' => 'get', 'class' => 'form-inline js-confirm', 'data-confirm' => $belum_terlaksana_message]) !!}  
				{!! Form::submit('Belum Terlaksana', ['class'=>'btn btn-primary  btn-sm']) !!} 
				{!! Form::close()!!}
			@endif
		@endrole
	@elseif($model->status_jadwal == 2 ) 
		@role('admin') 
			{!! Form::model($model, ['url' => $belum_terlaksana_url, 'method' => 'get', 'class' => 'form-inline js-confirm', 'data-confirm' => $belum_terlaksana_message]) !!}  
			{!! Form::submit('Belum Terlaksana', ['class'=>'btn btn-primary  btn-sm']) !!} 
			{!! Form::close()!!}
		@endrole
		@role('dosen') 
			@if($model_user > 0)
				{!! Form::model($model, ['url' => $belum_terlaksana_url, 'method' => 'get', 'class' => 'form-inline js-confirm', 'data-confirm' => $belum_terlaksana_message]) !!}  
				{!! Form::submit('Belum Terlaksana', ['class'=>'btn btn-primary  btn-sm']) !!} 
				{!! Form::close()!!}
			@endif
		@endrole

	
	@endif

