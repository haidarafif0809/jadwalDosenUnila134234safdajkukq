@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li><a href="{{ url('/home') }} ">Home</a></li>
                    <li><a href="{{ url('/admin/setting_slide') }}"> Setting Slide</a></li>
                    <li class="active">Edit Setting Slide</li>
                </ul>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="panel-title">Edit Setting Slide</h2>
                    </div>

                    <div class="panel-body">
                  {!! Form::model($setting_slide, ['url' => route('setting_slide.update', $setting_slide->id),
                        'method' => 'put', 'files'=>'true', 'class'=>'form-horizontal']) !!} 

                        <!-- KOLOM slide  1-->
                        <div class="form-group{{ $errors->has('slide_1') ? ' has-error' : '' }}">
                            {!! Form::label('slide_1', 'Slide Pertama', ['class' => 'col-md-2 control-label']) !!}
                            <div class="col-md-2">
                                {!! Form::file('slide_1') !!}
                                    @if (isset($setting_slide) && $setting_slide->slide_1)
                                        <p>
                                            {!! Html::image(asset('img/'.$setting_slide->slide_1), null, ['class' => 'img-rounded img-responsive']) !!}
                                        </p>
                                    @endif
                                {!! $errors->first('slide_1', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('judul_slide_1') ? ' has-error' : '' }}">
                            {!! Form::label('judul_slide_1', 'Judul Slide Pertama', ['class'=>'col-md-2 control-label']) !!}
                            <div class="col-md-4">
                                {!! Form::text('judul_slide_1', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
                                {!! $errors->first('judul_slide_1', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>

                        <!-- KOLOM slide  2-->
                        <div class="form-group{{ $errors->has('slide_2') ? ' has-error' : '' }}">
                            {!! Form::label('slide_2', 'Slide Kedua', ['class' => 'col-md-2 control-label']) !!}
                            <div class="col-md-2">
                                {!! Form::file('slide_2') !!}
                                    @if (isset($setting_slide) && $setting_slide->slide_2)
                                        <p>
                                            {!! Html::image(asset('img/'.$setting_slide->slide_2), null, ['class' => 'img-rounded img-responsive']) !!}
                                        </p>
                                    @endif
                                {!! $errors->first('slide_2', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('judul_slide_2') ? ' has-error' : '' }}">
                            {!! Form::label('judul_slide_2', 'Judul Slide Kedua', ['class'=>'col-md-2 control-label']) !!}
                            <div class="col-md-4">
                                {!! Form::text('judul_slide_2', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
                                {!! $errors->first('judul_slide_2', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>

                        <!-- KOLOM slide  2-->
                        <div class="form-group{{ $errors->has('slide_3') ? ' has-error' : '' }}">
                            {!! Form::label('slide_3', 'Slide Ketiga', ['class' => 'col-md-2 control-label']) !!}
                            <div class="col-md-2">
                                {!! Form::file('slide_3') !!}
                                    @if (isset($setting_slide) && $setting_slide->slide_3)
                                        <p>
                                            {!! Html::image(asset('img/'.$setting_slide->slide_3), null, ['class' => 'img-rounded img-responsive']) !!}
                                        </p>
                                    @endif
                                {!! $errors->first('slide_3', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('judul_slide_3') ? ' has-error' : '' }}">
                            {!! Form::label('judul_slide_3', 'Judul Slide Ketiga', ['class'=>'col-md-2 control-label']) !!}
                            <div class="col-md-4">
                                {!! Form::text('judul_slide_3', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
                                {!! $errors->first('judul_slide_3', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>

                        <!-- TOMBOL SIMPAN -->
                        <div class="form-group">
                            <div class="col-md-4 col-md-offset-2">
                            {!! Form::submit('Simpan', ['class'=>'btn btn-primary']) !!}
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
    