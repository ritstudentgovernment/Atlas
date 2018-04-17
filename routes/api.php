 <?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/spots','SpotController@index');
Route::get('/spots/{spot_id}','SpotController@get');
Route::post('/spots/create','SpotController@store');
Route::post('/spots/update/{spot_id}','SpotController@update');

Route::get('/admin/users','UserController@index')->middleware('permission:administer');
Route::post('/admin/users/promote/{id}/reviewer','UserController@promoteReviewer')->middleware('permission:administer');
Route::post('/admin/users/promote/{id}/admin','UserController@promoteAdmin')->middleware('permission:administer');