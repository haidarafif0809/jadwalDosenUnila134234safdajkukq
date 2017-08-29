<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index')->name('home');

Route::get('/jadwal-kuliah', 'HomeController@jadwal_kuliah');


Auth::routes();

Route::post('/data-modul-per-block',[
	'middleware' => ['auth'],
	'as' => 'modul.data_modul_perblock',
	'uses' => 'HomeController@data_modul_perblock'
	]);

Route::post('/data-modul-per-block-penjadwalan',[
	'middleware' => ['auth'],
	'as' => 'modul.data_modul_perblock_penjadwalan',
	'uses' => 'PenjadwalanController@data_modul_perblock_penjadwalan'
	]);

Route::post('/tanggal-modul-per-block-penjadwalan',[
	'middleware' => ['auth'],
	'as' => 'modul.tanggal_modul_perblock_penjadwalan',
	'uses' => 'PenjadwalanController@tanggal_modul_perblock_penjadwalan'
	]);
Route::post('/data-info-jadwal',[
	'middleware' => ['auth'],
	'as' => 'jadwal.info',
	'uses' => 'HomeController@info_jadwal'
	]);
Route::get('/jadwal-mahasiswa',[
	'middleware' => ['auth','role:mahasiswa'],
	'as' => 'jadwal.mahasiswa',
	'uses' => 'HomeController@proses_jadwal_mahasiswa'
	]);
Route::get('/lihat-jadwal-permodul/{id_modul}/{id_block}',[
	'middleware' => ['auth','role:admin|pimpinan|pj_dosen'],
	'as' => 'modul.jadwal',
	'uses' => 'MasterBlockController@lihat_jadwal_permodul'
	]);
Route::get('/jadwal-dosen',[
	'middleware' => ['auth','role:dosen'],
	'as' => 'jadwal.dosen',
	'uses' => 'HomeController@proses_jadwal_dosen'
	]);

	Route::get('/home', 'HomeController@index')->name('home'); 


	Route::get('/ubah-password',[
	'middleware' => ['auth'],
	'as' => 'users.ubah_password',
	'uses' => 'UbahPasswordController@ubah_password'
	]);

	Route::put('/proses-ubah-password/{id}',[
	'middleware' => ['auth'],
	'as' => 'users.proses_ubah_password',
	'uses' => 'UbahPasswordController@proses_ubah_password'
	]);

	Route::get('admin/penjadwalans/filter',[
	'middleware' => ['auth','role:admin|pimpinan|pj_dosen'],
	'as' => 'penjadwalans.filter',
	'uses' => 'PenjadwalanController@filter'
	]);	

	Route::get('penjadwalans/batal',[
	'middleware' => ['auth','role:dosen'],
	'as' => 'penjadwalans.batal_dosen',
	'uses' => 'PenjadwalanController@status_batal_dosen'
	]);

	Route::post('home/analisa_jadwal',[
	'middleware' => ['auth'],
	'as' => 'analisa.jadwal',
	'uses' => 'HomeController@analisa_jadwal'
	]);

	Route::get('home/analisa_jadwal', 'HomeController@index');

	Route::post('home/table_terlaksana',[
	'middleware' => ['auth'],
	'as' => 'table.terlaksana',
	'uses' => 'HomeController@table_terlaksana'
	]);

	Route::post('home/table_belum_terlaksana',[
	'middleware' => ['auth'],
	'as' => 'table.belum_terlaksana',
	'uses' => 'HomeController@table_belum_terlaksana'
	]);

	Route::post('home/table_batal',[
	'middleware' => ['auth'],
	'as' => 'table.batal',
	'uses' => 'HomeController@table_batal'
	]);



Route::group(['prefix'=>'admin', 'middleware'=>['auth', 'role:admin|pimpinan|pj_dosen']], function () {

	Route::resource('master_ruangans', 'MasterRuanganController'); 
	Route::resource('master_mata_kuliahs', 'MasterMataKuliahController'); 
	Route::resource('master_blocks', 'MasterBlockController'); 
	Route::resource('master_users', 'MasterUserController'); 
	Route::resource('master_otoritas', 'MasterOtoritasController'); 
	Route::resource('penjadwalans', 'PenjadwalanController'); 
	Route::resource('modul', 'ModulController'); 
	Route::resource('settingwaktu', 'SettingWaktuController'); 
	Route::resource('angkatan', 'AngkatanController'); 

	//filter jadwal dosen dan ruangan 
	Route::get('penjadwalans/belumterlaksana/{id}',[
	'middleware' => ['auth'],
	'as' => 'penjadwalans.belumterlaksana',
	'uses' => 'PenjadwalanController@status_belum_terlaksana'
	]);


	Route::get('penjadwalans/terlaksana/{id}',[
	'middleware' => ['auth'],
	'as' => 'penjadwalans.terlaksana',
	'uses' => 'PenjadwalanController@status_terlaksana'
	]);

	Route::get('penjadwalans/batal/{id}',[
	'middleware' => ['auth'],
	'as' => 'penjadwalans.batal',
	'uses' => 'PenjadwalanController@status_batal'
	]);

	Route::get('master_users/no_konfirmasi/{id}',[
	'middleware' => ['auth'],
	'as' => 'master_users.no_konfirmasi',
	'uses' => 'MasterUserController@no_konfirmasi'
	]);	
	Route::get('master_blocks/modul/{id}',[
	'middleware' => ['auth','role:admin|pimpinan|pj_dosen'],
	'as' => 'master_blocks.modul',
	'uses' => 'MasterBlockController@createModul'
	]);	
	Route::get('master_blocks/mahasiswa/{id}',[
	'middleware' => ['auth','role:admin|pimpinan|pj_dosen'],
	'as' => 'master_blocks.mahasiswa',
	'uses' => 'MasterBlockController@createMahasiswa'
	]);

	Route::put('/proses-kait-modul-blok/{id}',[
	'middleware' => ['auth','role:admin'],
	'as' => 'master_blocks.proses_kait_modul_blok',
	'uses' => 'MasterBlockController@proses_kait_modul_blok'
	]);	
	Route::put('/hapus-mahasiswa-block/{id}',[
	'middleware' => ['auth','role:admin'],
	'as' => 'mahasiswa_block.destroy',
	'uses' => 'MasterBlockController@hapus_mahasiswa_block'
	]);
	Route::put('/hapus-modul-block/{id}',[
	'middleware' => ['auth','role:admin'],
	'as' => 'modul_block.destroy',
	'uses' => 'MasterBlockController@hapus_modul_block'
	]);

	Route::put('/proses-kait-mahasiswa-blok/{id}',[
	'middleware' => ['auth','role:admin'],
	'as' => 'master_blocks.proses_kait_mahasiswa_blok',
	'uses' => 'MasterBlockController@proses_kait_mahasiswa_blok'
	]);

	Route::get('master_users/konfirmasi/{id}',[
	'middleware' => ['auth','role:admin'],
	'as' => 'master_users.konfirmasi',
	'uses' => 'MasterUserController@konfirmasi'
	]);

	Route::get('master_users/reset/{id}',[
	'middleware' => ['auth','role:admin'],
	'as' => 'master_users.reset',
	'uses' => 'MasterUserController@reset'
	]);



});

// route android 

Route::post('/login_android', "AndroidController@authenticate");
Route::post('/tambah_ruangan', "AndroidController@tambah_ruangan");
Route::post('/list_ruangan', "AndroidController@list_ruangan");




