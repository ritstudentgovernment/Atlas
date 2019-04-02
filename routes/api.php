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

 Route::prefix('/spots')->group(function () {
     // Publicly accessible routes
     Route::get('/', 'SpotController@get');
     Route::get('/categories', 'CategoryController@get');
     // Routes protected by authentication
     Route::middleware(['auth:api'])->group(function () {
         Route::prefix('/create')->group(function () {
             Route::post('/', 'SpotController@store');
             Route::get('/', 'SpotController@getDefaults');
         });
         Route::post('/approve/{spot}', 'SpotController@approve')->middleware(['permission:approve spots']);
     });
 });

Route::prefix('admin')->middleware(['permission:administer'])->group(function () {
    Route::prefix('spots')->group(function () {
        Route::prefix('category')->group(function () {
            Route::prefix('{category}')->group(function () {
                Route::post('update', 'CategoryController@update');
            });
        });
        Route::prefix('type')->group(function () {
            Route::post('create', 'TypeController@store');
            Route::prefix('{type}')->group(function () {
                Route::post('delete', 'TypeController@delete');
            });
        });
        Route::prefix('classification')->group(function () {
            Route::post('create', 'ClassificationController@store');
            Route::prefix('{classification}')->group(function () {
                Route::post('update', 'ClassificationController@update');
                Route::post('delete', 'ClassificationController@delete');
                Route::post('delete/soft', 'ClassificationController@softDelete');
            });
        });
    });
    Route::prefix('/users')->group(function () {
        Route::get('/', 'UserController@index');
        Route::post('/promote/{user}/reviewer', 'UserController@promoteReviewer');
        Route::post('/promote/{user}/admin', 'UserController@promoteAdmin');
    });
});
