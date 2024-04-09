<?php

use Illuminate\Support\Facades\Route;
use Pterodactyl\Http\Controllers\Auth\SingleSignOnController;

Route::group(['middleware' => 'guest'], function () {

    Route::get('/auth/login/sso', [SingleSignOnController::class, 'Driver']);
    Route::get('/auth/login/sso/redirect', [SingleSignOnController::class, 'DriverCallback']);
    
});

