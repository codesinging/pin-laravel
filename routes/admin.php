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

        Route::put('administrators/{administrator}/permit', [Admin\AdministratorController::class, 'permit']);
        Route::put('administrators/{administrator}/assign', [Admin\AdministratorController::class, 'assign']);
        Route::apiResource('administrators', Admin\AdministratorController::class);

        Route::put('admin_roles/{admin_role}/permit', [Admin\AdminRoleController::class, 'permit']);
        Route::apiResource('admin_roles', Admin\AdminRoleController::class);

    });
