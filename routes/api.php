<?php

use Doctrine\DBAL\Driver\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ClientAuth;
use App\Http\Middleware\UserAuth;


use App\Http\Controllers\User\ProjectTypeController;
use App\Http\Controllers\User\ProjectController;
use App\Http\Controllers\User\PackageController;
use App\Http\Controllers\User\IndexController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::group(['prefix' => '/v2',], function () {

    //Auth URLS
    Route::group(['prefix' => '/auth', 'namespace' => 'Auth',], function () {
        Route::group(['prefix' => '/client'], function () {
            Route::post('/send_verification_mail', 'ClientAuthController@sendVerificationMail');
            Route::post('/verify_email_token', 'ClientAuthController@verifyEmailToken');
            Route::post('/register', 'ClientAuthController@register');
            Route::post('/login', 'ClientAuthController@login');
            Route::get('/google/get_url', 'GoogleController@getAuthUrl');
            Route::post('/google/login', 'GoogleController@postLogin');
        });
        Route::group(['prefix' => '/user'], function () {
            Route::post('/login', 'UserAuthController@login');
            Route::post('/send_password_reset_code', 'UserAuthController@sendPasswordResetCode');
            Route::post('/verify_password_reset_code', 'UserAuthController@verifyPasswordResetToken');
            Route::post('/reset_password', 'UserAuthController@resetPassword');
        });
    });

    //User/Admin/Staff Routes
    Route::group(['middleware' => UserAuth::class, 'prefix' => '/user', 'namespace' => 'User',], function () {
        Route::get('/dashboard', [IndexController::class, "dashboard"]);

        Route::group(['prefix' => '/profile'], function () {
            Route::post('/set_password', 'ProfileController@setPassword');
            Route::post('/update', 'ProfileController@update');
        });
        Route::post('/upload_photo', 'FileController@savePhoto');

        //Project Types
        Route::group(['prefix' => '/project_types'], function () {
            Route::post('/update', [ProjectTypeController::class, "update"]);
            Route::get('', [ProjectTypeController::class, "projectTypes"]);
            Route::get('/{id}', [ProjectTypeController::class, "projectType"]);
        });
        // Project
        Route::group(['prefix' => '/projects'], function () {
            Route::post('', [ProjectController::class, "save"]);
            Route::patch('', [ProjectController::class, "update"]);
            Route::post('/activate', [ProjectController::class, "activate"]);
            Route::post('/deactivate', [ProjectController::class, "deactivate"]);
            Route::post('/delete', [ProjectController::class, "delete"]);
            Route::post('/filter/{projectTypeId}', [ProjectController::class, "filter"]);
            Route::get('types', [ProjectController::class, "types"]);
            Route::get('/summary/{projectTypeId}', [ProjectController::class, "summary"]);
            Route::get('/all/{projectTypeId}', [ProjectController::class, "projects"]);
            Route::get('/search/{projectTypeId}', [ProjectController::class, "search"]);
            Route::get('/export/{projectTypeId}', [ProjectController::class, "export"]);
            Route::get('/{id}', [ProjectController::class, "project"]);
        });
        // Package
        Route::group(['prefix' => '/packages'], function () {
            Route::post('', [PackageController::class, "save"]);
            Route::patch('', [PackageController::class, "update"]);
            Route::patch('/mark_as_sold_out', [PackageController::class, "markAsSoldOut"]);
            Route::patch('/mark_as_in_stock', [PackageController::class, "markAsInStock"]);
            Route::post('/activate', [PackageController::class, "activate"]);
            Route::post('/deactivate', [PackageController::class, "deactivate"]);
            Route::post('/delete', [PackageController::class, "delete"]);
            Route::post('/filter/{projectId}', [PackageController::class, "filter"]);
            Route::get('/all/{projectId}', [PackageController::class, "packages"]);
            Route::get('/search/{projectId}', [PackageController::class, "search"]);
            Route::get('/export/{projectId}', [PackageController::class, "export"]);
            Route::get('/{id}', [PackageController::class, "package"]);
        });
    });

    // Client Routes
    Route::group(['middleware' => ClientAuth::class, 'prefix' => '/client', 'namespace' => 'Client',], function () {
        // Client Profile
        Route::group(['prefix' => '/profile',], function () {
            Route::post('/update', 'ClientController@update');
            Route::post('/save_next_of_kin', 'ClientController@addNextOfKin');
        });
        Route::post('/upload_photo', 'FileController@savePhoto');
    });

});