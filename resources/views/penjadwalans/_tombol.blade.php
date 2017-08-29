@role('admin') 
	@if($model->status_jadwal == 0 )  
		{!! Form::model($model, ['url' => $form_url, 'method' => 'delete', 'class' => 'form-inline js-confirm', 'data-confirm' => $confirm_message]) !!}

		<a href="{{ $edit_url }}" class="btn btn-sm btn-success">Ubah</a> |

		{!! Form::submit('Hapus',['class'=>'btn btn-sm btn-danger js-confirm']) !!}
		{!! Form::close() !!} 
	@elseif($model->status_jadwal == 1)
		{!! Form::model($model, ['url' => $form_url, 'method' => 'delete', 'class' => 'form-inline js-confirm', 'data-confirm' => $confirm_message]) !!}

		<a href="{{ $edit_url }}" class="btn btn-sm btn-success">Ubah</a> |

		{!! Form::submit('Hapus',['class'=>'btn btn-sm btn-danger js-confirm']) !!}
		{!! Form::close() !!} 
	@elseif($model->status_jadwal == 2)
		<p style="color: red;font-size: 12px;">Maaf Anda Tidak Bisa Mengubah Dan Menghapus</p>
	@endif
@endrole  