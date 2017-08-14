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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/jadwal-kuliah', 'HomeController@jadwal_kuliah');

Auth::routes();

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
	'middleware' => ['auth','role:admin'],
	'as' => 'penjadwalans.filter',
	'uses' => 'PenjadwalanController@filter'
	]);	

Route::group(['prefix'=>'admin', 'middleware'=>['auth', 'role:admin']], function () {

	Route::resource('master_ruangans', 'MasterRuanganController'); 
	Route::resource('master_mata_kuliahs', 'MasterMataKuliahController'); 
	Route::resource('master_blocks', 'MasterBlockController'); 
	Route::resource('master_users', 'MasterUserController'); 
	Route::resource('master_otoritas', 'MasterOtoritasController'); 
	Route::resource('penjadwalans', 'PenjadwalanController'); 
	Route::resource('modul', 'ModulController'); 

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
	'middleware' => ['auth','role:admin'],
	'as' => 'master_blocks.modul',
	'uses' => 'MasterBlockController@createModul'
	]);	
	Route::get('master_blocks/mahasiswa/{id}',[
	'middleware' => ['auth','role:admin'],
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