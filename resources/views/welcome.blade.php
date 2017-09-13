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
          @if($setting_slide->count() > 0)
            <div id="myCarousel" class="carousel slide" data-ride="carousel">
              <!-- Indicators -->
              <ol class="carousel-indicators">
              @foreach($setting_slide as $setting_slides)
              <!-- $loop->iteration berfunsi untuk menghitun data menjadi 1 sesaui jumlah data -->
                <li data-target="#myCarousel" data-slide-to="{{ $loop->iteration }}" ></li> 
              @endforeach 
              </ol>

               <!-- Wrapper for slides -->
              <div class="carousel-inner"> 
               <!-- $loop->first untuk menampilkan kode yang awal dan seterusnya -->
                    @foreach($setting_slide as $setting_slides)
                      @if ($loop->first)
                        <div class="item active">
                      @else
                       <div class="item">            
                      @endif
                            <img src="{{ asset('img/'.$setting_slides->slide)}}" style="width:100%;">
                            <div class="carousel-caption"> 
                              <p>{{ $setting_slides->judul_slide}}</p>
                            </div>
                          </div> 
                      @endforeach 
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
              </div><br> 
          @endif

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
