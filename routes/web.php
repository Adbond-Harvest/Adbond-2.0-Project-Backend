<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email_test', function () {
    return view('emails/email_verification', ['code' => 12345]);
});
