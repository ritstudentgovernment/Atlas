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

Route::get('/', 'SpotController@index');

Route::get('splash', function () {

    return view('splash');

});

Route::redirect('/login', '/shibboleth-login')->name('login');
Route::redirect('/logout', '/shibboleth-logout')->name('logout');
