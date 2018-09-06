 <?php

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

Route::get('/spots/categories','SpotCategoryController@get');
Route::get('/spots/{spot_id}','SpotController@get');
Route::get('/spots','SpotController@get');

Route::post('/spots/approve/{spot_id}','SpotController@update')->middleware('permission:approve spots');
Route::post('/spots/create','SpotController@store')->middleware('permission:add spot');

Route::group(['middleware' => ['permission:administer']], function () {
    Route::get('/admin/users','UserController@index');
    Route::post('/admin/users/promote/{user}/reviewer','UserController@promoteReviewer');
    Route::post('/admin/users/promote/{user}/admin','UserController@promoteAdmin');
});
