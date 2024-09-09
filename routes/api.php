<?php

use Doctrine\DBAL\Driver\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CustomerAuth;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::group(['prefix' => '/v2',], function () {

    //Auth URLS
    Route::group(['prefix' => '/auth', 'namespace' => 'Auth',], function () {
        Route::group(['prefix' => '/customer'], function () {
            Route::post('/send_verification_mail', 'CustomerAuthController@sendVerificationMail');
            Route::post('/verify_email_token', 'CustomerAuthController@verifyEmailToken');
            Route::post('/register', 'CustomerAuthController@register');
            Route::post('/login', 'CustomerAuthController@login');
            Route::get('/google/get_url', 'GoogleController@getAuthUrl');
            Route::post('/google/login', 'GoogleController@postLogin');
        });
        // Route::post('/validate_email_token', 'CustomerAuthController@validateEmailVerificationToken')->name('validate_email_token');
        // Route::post('/resend_email_token', 'CustomerAuthController@resend_email_token')->name('resend_email_token');
        // Route::post('/login', 'CustomerAuthController@login');
        // Route::get('/google/get_url', 'GoogleController@getAuthUrl');
        // Route::post('/google/auth', 'GoogleController@postLogin');
        // Route::post('send_password_reset_link', 'CustomerAuthController@send_password_reset_link');
        // Route::post('verify_reset_hash', 'CustomerAuthController@verify_password_reset_signature');
        // Route::post('reset_password', 'CustomerAuthController@reset_password');
    });
    // Customer Routes
    // Route::middleware([CustomerAuth::class])->group(function() {
    Route::group(['middleware' => CustomerAuth::class, 'prefix' => '/customer', 'namespace' => 'Customer',], function () {
        // Customer Profile
        Route::group(['prefix' => '/profile',], function () {
            Route::post('/update', 'CustomerController@update');
            Route::post('/save_next_of_kin', 'CustomerController@addNextOfKin');
        });
        Route::post('/upload_photo', 'FileController@savePhoto');
    });

});