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
 
  <div id="myCarousel" class="carousel slide" data-ride="carousel">
    <!-- Indicators -->
    <ol class="carousel-indicators">
      <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
      <li data-target="#myCarousel" data-slide-to="1"></li>
      <li data-target="#myCarousel" data-slide-to="2"></li>
    </ol>

     <!-- Wrapper for slides -->
        <div class="carousel-inner">

          <div class="item active">
        <img src="{{ asset('img/'.$setting_slide->slide_1)}}" style="width:100%;">
            <div class="carousel-caption"> 
              <p>{{ $setting_slide->judul_slide_1}}</p>
            </div>
          </div>

          <div class="item">
        <img src="{{ asset('img/'.$setting_slide->slide_2)}}" style="width:100%;">
            <div class="carousel-caption"> 
              <p>{{ $setting_slide->judul_slide_2}}</p>
            </div>
          </div>
        
          <div class="item">
        <img src="{{ asset('img/'.$setting_slide->slide_3)}}" style="width:100%;">
            <div class="carousel-caption"> 
              <p>{{ $setting_slide->judul_slide_3}}</p>
            </div>
          </div>
      
        </div>
 

    <!-- Left and right controls -->
    <a class="left carousel-control" href="#myCarousel" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#myCarousel" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right"></span>
      <span class="sr-only">Next</span>
    </a>
  </div> <br> 
            
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
