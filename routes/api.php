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

 use Illuminate\Support\Facades\Auth;

 Route::prefix('spots')->group(function () {
     Route::get('/', 'SpotController@get');
     Route::get('/{spot_id}', 'SpotController@get');
     Route::get('/categories', 'CategoryController@get');

     Route::post('/create', 'SpotController@store')->middleware(['permission:add spot']);
     Route::post('/approve/{spot}', 'SpotController@approve')->middleware(['permission:approve spots']);
 });

 Route::get('/checklogin', function () {
     return [Auth::check(), Auth::guest(), Auth::user()];
 });

Route::prefix('admin')->middleware(['permission:administer'])->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('/', 'UserController@index');
        Route::post('/promote/{user}/reviewer', 'UserController@promoteReviewer');
        Route::post('/promote/{user}/admin', 'UserController@promoteAdmin');
    });
});
