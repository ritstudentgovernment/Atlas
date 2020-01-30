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
             Route::get('/', 'SpotController@getDefaults');
             Route::post('/', 'SpotController@store');
         });
         Route::prefix('{spot}')->middleware(['permission:approve spots'])->group(function () {
             Route::post('/approve', 'SpotController@approve');
             Route::post('/delete', 'SpotController@delete');
         });
     });
 });

Route::prefix('admin')->middleware(['permission:administer'])->group(function () {
    Route::prefix('spots')->group(function () {
        Route::get('stats', 'SpotController@stats');
        Route::prefix('category')->group(function () {
            Route::post('create', 'CategoryController@store');
            Route::prefix('{category}')->group(function () {
                Route::post('update', 'CategoryController@update');
                Route::post('delete', 'CategoryController@delete');
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
        Route::prefix('descriptor')->group(function () {
            Route::get('list', 'DescriptorController@list');
            Route::post('create', 'DescriptorController@store');
            Route::prefix('{descriptor}')->group(function () {
                Route::get('/', 'DescriptorController@get');
                Route::post('update', 'DescriptorController@update');
                Route::post('delete', 'DescriptorController@delete');
            });
        });
        Route::prefix('upload')->group(function () {
            Route::post('/', 'ImportController@uploadSpotsCSV');
            Route::post('descriptors', 'ImportController@uploadDescriptorsCSV');
            Route::post('run', 'ImportController@runImport');
        });
    });
    Route::prefix('users')->group(function () {
        Route::get('/', 'UserController@all');
        Route::get('staff', 'UserController@staff');
        Route::get('permissions', 'PermissionController@all');
        Route::prefix('{user}')->group(function () {
            Route::get('/', 'UserController@get');
            Route::prefix('promote')->group(function () {
                Route::post('reviewer', 'UserController@promoteReviewer');
                Route::post('admin', 'UserController@promoteAdmin');
            });
            Route::prefix('demote')->group(function () {
                Route::post('reviewer', 'UserController@demoteReviewer');
                Route::post('admin', 'UserController@demoteAdmin');
            });
        });
    });
});
