@extends('layouts.app')
@section('content')

<style type="text/css">
    #filter {
        margin-top: 10px;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <ul class="breadcrumb">
                <li><a href="{{ url('/home') }}">Home</a></li>
                <li class="active">Penjadwalan</li>
            </ul>
 
            
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">Penjadwalan</h2>
                </div>

                <div class="panel-body"> 
                     <a class="btn btn-primary" href="{{ url('/') }}">Hari Ini</a>
                     <a class="btn btn-primary" href="{{ url('/besok') }}">Besok</a>
                     <a class="btn btn-primary" href="{{ url('/lusa') }}">Lusa</a>
                    <div class="table-responsive"> 
                    {!! $html->table(['class'=>'table-striped table']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! $html->scripts() !!} 
@endsection
