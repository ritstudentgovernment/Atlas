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

use App\Spot;
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

Route::prefix('admin')->name('admin.')->namespace('Admin')->group(function () {
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::prefix('spots')->name('spots.')->group(function () {
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', 'DashboardController@categories')->name('all');
            Route::prefix('{category}')->group(function () {
                Route::get('/', 'DashboardController@category')->name('category');
            });
        });
    });
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('all', 'DashboardController@users')->name('all');
        Route::get('staff', 'DashboardController@staff')->name('staff');
    });
    Route::get('settings', 'DashboardController@settings')->name('settings');
    Route::prefix('mailables')->name('mailables.')->group(function () {
        Route::get('spots/{template}', function ($template) {
            $className = 'App\Mail\Spot'.ucfirst($template);
            if (class_exists($className)){
                $spot = Spot::where('approved', 0)->first();
                return new $className($spot);
            }
        });
    });
});
