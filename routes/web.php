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

use Illuminate\Support\Facades\Route;

Route::get('/', 'Pages\HomeController@index')->name('home');
Route::get('/about', function () {
    return view('pages.about');
})->name('about');
Route::get('/splash', function () {
    return view('pages.splash');
})->name('splash');

Route::get('login', 'SAMLController@login');
Route::get('logout', 'SAMLController@logout');

/* Admin Pages */

Route::prefix('admin')->group(function () {

    Route::get('/', function () {
        return view('pages.admin');
    });

});