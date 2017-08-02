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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::group(['prefix'=>'admin', 'middleware'=>['auth', 'role:admin']], function () {

	Route::resource('master_ruangans', 'MasterRuanganController'); 
	Route::resource('master_mata_kuliahs', 'MasterMataKuliahController'); 
	Route::resource('master_blocks', 'MasterBlockController'); 
	Route::resource('master_users', 'MasterUserController'); 


	Route::get('master_users/no_konfirmasi/{id}',[
	'middleware' => ['auth'],
	'as' => 'master_users.no_konfirmasi',
	'uses' => 'MasterUserController@no_konfirmasi'
	]);

	Route::get('master_users/konfirmasi/{id}',[
	'middleware' => ['auth'],
	'as' => 'master_users.konfirmasi',
	'uses' => 'MasterUserController@konfirmasi'
	]);
});