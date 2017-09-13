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
                        @include('setting_slide._form')
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
    