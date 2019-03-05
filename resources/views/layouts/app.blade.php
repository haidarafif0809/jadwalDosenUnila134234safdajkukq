<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles --> 
    <link href="{{ asset('css/font-awesome.min.css') }}" rel='stylesheet' type='text/css'>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dataTables.bootstrap.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/selectize.bootstrap3.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-datepicker.min.css') }}" rel="stylesheet">  
    <link href="{{ asset('css/bootstrap-clockpicker.min.css') }}" rel="stylesheet">  
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}"> <!-- CSS reset -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"> <!-- Resource style -->


</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        <li><a href="http://anjungan.fk.unila.ac.id/">Agenda Pimpinan</a></li> 

                    @if (!Auth::guest())
                        <li><a href="{{ route('home') }}">Beranda</a></li>
                    @endif
                    @role('admin')
                        <li><a href="{{ route('penjadwalans.index') }}">Penjadwalan</a></li>
                        <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                   Master Data <span class="caret"></span>
                                </a>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                            <li><a href="{{ route('master_ruangans.index') }}">Ruangan</a></li>
                            <li><a href="{{ route('master_users.index') }}">User</a></li><li><a href="{{ route('angkatan.index') }}">Angkatan</a></li>
                            <li><a href="{{ route('master_mata_kuliahs.index') }}">Mata Kuliah</a></li> 
                            <li><a href="{{ route('master_blocks.index') }}">Blok</a></li>   
                            <li><a href="{{ route('materi.index') }}">Materi</a></li>  
                            <li><a href="{{ route('modul.index') }}">Modul</a></li>  
                            <li><a href="{{ route('kelompok_mahasiswa.index') }}">Kelompok Mahasiswa</a></li>  
                          </ul>
                        </li>
                        <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                   Setting <span class="caret"></span>
                                </a>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">                           
                            <li><a href="{{ route('settingwaktu.index') }}">Setting Waktu</a></li>             
                            <li><a href="{{ route('setting_slide.index') }}">Setting Slide</a></li>
                          </ul>
                        </li>

                        <!-- DROPDOWN LAPORAN -->
                        <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                   Laporan <span class="caret"></span>
                                </a>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">                           
                            <li><a href="{{ route('laporan_rekap_presensi_dosen.index') }}">Laporan Presensi Dosen</a></li>    
                            <li><a href="{{ route('laporan_presensi_mahasiswa.index') }}">Laporan Presensi Mahasiswa</a></li>             
                          </ul>
                        </li>
                        <!-- DROPDOWN LAPORAN -->
                    @endrole  
                    @role('perekap')
                        <li><a href="{{ route('penjadwalans.index') }}">Penjadwalan</a></li>  

                             <!-- DROPDOWN LAPORAN -->
                        <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                   Laporan <span class="caret"></span>
                                </a>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">                           
                            <li><a href="{{ route('laporan_rekap_presensi_dosen.index') }}">Laporan Presensi Dosen</a></li>    
                            <li><a href="{{ route('laporan_presensi_mahasiswa.index') }}">Laporan Presensi Mahasiswa</a></li>             
                          </ul>
                        </li>
                        <!-- DROPDOWN LAPORAN -->

                    @endrole


                    @role('pj_dosen')  
                     <li><a href="{{ route('master_mata_kuliahs.index') }}">Mata Kuliah</a></li> 
                      <li><a href="{{ route('materi.index') }}">Materi</a></li>  
                    <li><a href="{{ route('kelompok_mahasiswa.index') }}">Kelompok Mahasiswa</a></li>  

                    @endrole
                    @role('pimpinan')  
                        <li><a href="{{ route('penjadwalans.index') }}">Penjadwalan</a></li> 
                        <li><a href="{{ route('master_blocks.index') }}">Blok</a></li>      
                    @endrole 

                    @role('dosen')  
                        <li><a href="{{ route('penjadwalans.index') }}">Penjadwalan</a></li> 
                        <li><a href="{{ route('master_blocks.index') }}">Blok</a></li>      
                    @endrole 
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ url('/ubah-password') }}">Ubah Password</a>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
        @include('layouts._flash')
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/modernizr.js') }}"></script>
     <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
<script src="{{ asset('js/tether.min.js') }}"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="{{ asset('js/jquery.dataTables.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap.js') }}"></script>
<script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('js/bootstrap-clockpicker.min.js') }}"></script>
<script src="{{ asset('js/bootstrap-clockpicker.js') }}"></script>
<script src="{{ asset('js/selectize.min.js') }}"></script> 
<script src="{{ asset('js/custom-v.1.0.1.js') }}"></script>
<script src="{{ asset('js/main.js') }}"></script> 
<script type="text/javascript">
    
       // $.fn.dataTable.ext.errMode = 'throw';
</script>

@yield('scripts')
</body>
</html>
