@extends('layouts.app')

@section('content')
<div class="container">

<center><h1>{{ $modul->modul->nama_modul }}</h1></center>
    <div class="cd-schedule loading">
    <div class="timeline">
        <ul> 
            <li><span>07:00</span></li>
            <li><span>07:30</span></li>
            <li><span>08:00</span></li>
            <li><span>08:30</span></li>
            <li><span>09:00</span></li>
            <li><span>09:30</span></li>
            <li><span>10:00</span></li>
            <li><span>10:30</span></li>
            <li><span>11:00</span></li>
            <li><span>11:30</span></li>
            <li><span>12:00</span></li>
            <li><span>12:30</span></li>
            <li><span>13:00</span></li>
            <li><span>13:30</span></li>
            <li><span>14:00</span></li>
            <li><span>14:30</span></li>
            <li><span>15:00</span></li>
            <li><span>15:30</span></li>
            <li><span>16:00</span></li>
            <li><span>16:30</span></li>
            <li><span>17:00</span></li>
            

         
        
        </ul>
    </div> <!-- .timeline -->

    <div class="events">
        <ul>
            <li class="events-group">
                <div class="top-info"><span>Senin</span></div>

                <ul>
                @foreach($jadwal_senin as $data)
                    <li class="single-event" data-start="{{$data['waktu_mulai']}}" data-end="{{$data['waktu_selesai']}}"  data-content="event-rowing-workout" data-event="event-2">
                        <a href="#0">
                            <em class="event-name">{{ $data['nama_mata_kuliah'] }}</em>
                        </a>
                    </li>
                @endforeach
                </ul>
            </li>

            <li class="events-group">
                <div class="top-info"><span>Selasa</span></div>

                <ul>

                @foreach($jadwal_selasa as $data)
                    <li class="single-event" data-start="{{$data['waktu_mulai']}}" data-end="{{$data['waktu_selesai']}}"  data-content="event-rowing-workout" data-event="event-2">
                        <a href="#0">
                            <em class="event-name">{{ $data['nama_mata_kuliah'] }}</em>
                        </a>
                    </li>
                @endforeach

                
                </ul>
            </li>

            <li class="events-group">
                <div class="top-info"><span>Rabu</span></div>

                <ul>
                @foreach($jadwal_rabu as $data)
                    <li class="single-event" data-start="{{$data['waktu_mulai']}}" data-end="{{$data['waktu_selesai']}}"  data-content="event-rowing-workout" data-event="event-2">
                        <a href="#0">
                            <em class="event-name">{{ $data['nama_mata_kuliah'] }}</em>
                        </a>
                    </li>
                @endforeach
                </ul>
            </li>

            <li class="events-group">
                <div class="top-info"><span>Kamis</span></div>

                <ul>
                @foreach($jadwal_kamis as $data)
                    <li class="single-event" data-start="{{$data['waktu_mulai']}}" data-end="{{$data['waktu_selesai']}}"  data-content="event-rowing-workout" data-event="event-2">
                        <a href="#0">
                            <em class="event-name">{{ $data['nama_mata_kuliah'] }}</em>
                        </a>
                    </li>
                @endforeach     
                </ul>
            </li>

            <li class="events-group">
                <div class="top-info"><span>Jumat</span></div>

                <ul>
                @foreach($jadwal_jumat as $data)
                    <li class="single-event" data-start="{{$data['waktu_mulai']}}" data-end="{{$data['waktu_selesai']}}"  data-content="event-rowing-workout" data-event="event-2">
                        <a href="#0">
                            <em class="event-name">{{ $data['nama_mata_kuliah'] }}</em>
                        </a>
                    </li>
                @endforeach

                </ul>
            </li>
        </ul>
    </div>

    <div class="event-modal">
        <header class="header">
            <div class="content">
                <span class="event-date"></span>
                <h3 class="event-name"></h3>
            </div>

            <div class="header-bg"></div>
        </header>

        <div class="body">
            <div class="event-info"></div>
            <div class="body-bg"></div>
        </div>

        <a href="#0" class="close">Close</a>
    </div>

    <div class="cover-layer"></div>
</div> <!-- .cd-schedule -->
</div>
@endsection
