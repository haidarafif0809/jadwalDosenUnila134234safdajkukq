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


         @if ($agent->isMobile()) <!--jika diakses mobile -->
            <div class="row">

                <div class="col-lg-5 col-xs-6 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-thumbs-o-up fa-4x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">{{ $jadwal_terlaksana }}</div>
                                    <div>Jadwal Terlaksana!</div>
                                </div>
                            </div>
                        </div>
                        <a href="#lihat_detail_terlaksana" id="lihat_table_terlaksana"> 
                        </a>
                    </div>
                </div>

                <div class="col-lg-5 col-xs-6 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-thumbs-o-down fa-4x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">{{ $jadwal_belum_terlaksana }}</div>
                                    <div>Belum Terlaksana!</div>
                                </div>
                            </div>
                        </div>
                        <a href="#lihat_detail_belum_terlaksana" id="lihat_table_belum_terlaksana"> 
                        </a>
                    </div>
                </div>
           
                <div class="col-lg-5 col-xs-6 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-times fa-4x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">{{ $jadwal_batal }}</div>
                                    <div>Jadwal Batal!</div>
                                </div>
                            </div>
                        </div>
                        <a href="#lihat_detail_batal" id="lihat_table_batal"> 
                        </a>
                    </div>
                </div>
               
                <div class="col-lg-5 col-xs-6 col-md-6">
                    <div class="panel panel-yellow">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-edit fa-4x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">{{ $jadwal_ubah_dosen }}</div>
                                    <div>Dosen Di Gantikan</div>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>

            </div>
        @else 
            <div class="row">
                <div class="col-lg-3 col-xs-4 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-thumbs-o-up fa-4x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">{{ $jadwal_terlaksana }}</div>
                                    <div>Jadwal Terlaksana!</div>
                                </div>
                            </div>
                        </div>
                        <a href="#lihat_detail_terlaksana" id="lihat_table_terlaksana"> 
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-4 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-thumbs-o-down fa-4x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">{{ $jadwal_belum_terlaksana }}</div>
                                    <div>Belum Terlaksana!</div>
                                </div>
                            </div>
                        </div>
                        <a href="#lihat_detail_belum_terlaksana" id="lihat_table_belum_terlaksana"> 
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-4 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-times fa-4x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">{{ $jadwal_batal }}</div>
                                    <div>Jadwal Batal!</div>
                                </div>
                            </div>
                        </div>
                        <a href="#lihat_detail_batal" id="lihat_table_batal"> 
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-4 col-md-6">
                    <div class="panel panel-yellow">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-edit fa-4x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">{{ $jadwal_ubah_dosen }}</div>
                                    <div>Dosen Di Gantikan</div>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>

            </div>
        @endif 
 




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
                            <img src="{{ asset('img/'.$setting_slides->slide)}}" style="width:100%;height: 500px;">
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
