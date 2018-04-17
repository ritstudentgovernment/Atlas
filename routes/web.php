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

Route::get('/', function(){

    $center_lat = (float)env("GOOGLE_MAPS_CENTER_LAT");
    $center_lng = (float)env("GOOGLE_MAPS_CENTER_LNG");
    $lat_range  = (float)env("GOOGLE_MAPS_LAT_CHANGE");
    $lng_range  = (float)env("GOOGLE_MAPS_LNG_CHANGE");

    return view('index', [

        "map" => [

            "api_key" => env("GOOGLE_MAPS_API_KEY"),
            "center"  => [
                "lat" => $center_lat,
                "lng" => $center_lng,
                "max_lat" => $center_lat + $lat_range,
                "min_lat" => $center_lat + $lat_range,
                "max_lng" => $center_lng + $lng_range,
                "mix_lng" => $center_lng + $lng_range
            ]

        ],
        "spots" => App\Spot::all(),
        "pin_icon_prefixes" => App\SpotCategory::all()->pluck("icon_prefix")

    ]);

});

Route::get('splash', function () {

    return view('splash');

});

Route::redirect('/login', '/shibboleth-login')->name('login');
Route::redirect('/logout', '/shibboleth-logout')->name('logout');