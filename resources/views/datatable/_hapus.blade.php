@role('admin')
{!! Form::model($model, ['url' => $form_url, 'method' => 'put', 'class' => 'form-inline js-confirm', 'data-confirm' => $confirm_message]) !!}


{!! Form::submit('Hapus',['class'=>'btn btn-sm btn-danger js-confirm']) !!}
{!! Form::close() !!}
@endrole