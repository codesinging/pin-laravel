<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;

Route::put('auth/login', [Admin\AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])
    ->group(function (){

        Route::put('auth/logout', [Admin\AuthController::class, 'logout']);
        Route::get('auth/user', [Admin\AuthController::class, 'user']);
        Route::put('auth/update', [Admin\AuthController::class, 'update']);
        Route::put('auth/password', [Admin\AuthController::class, 'password']);

        Route::apiResource('administrators', Admin\AdministratorController::class);
    });
