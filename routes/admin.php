<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;

Route::put('auth/login', [Admin\AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'admin.permission'])
    ->group(function () {

        Route::put('auth/logout', [Admin\AuthController::class, 'logout']);
        Route::get('auth/user', [Admin\AuthController::class, 'user']);
        Route::put('auth/update', [Admin\AuthController::class, 'update']);
        Route::put('auth/password', [Admin\AuthController::class, 'password']);

        Route::put('admin_users/{admin_user}/permit', [Admin\AdminUserController::class, 'permit']);
        Route::put('admin_users/{admin_user}/assign', [Admin\AdminUserController::class, 'assign']);
        Route::apiResource('admin_users', Admin\AdminUserController::class);

        Route::put('admin_roles/{admin_role}/permit', [Admin\AdminRoleController::class, 'permit']);
        Route::apiResource('admin_roles', Admin\AdminRoleController::class);

        Route::apiResource('admin_pages', Admin\AdminPageController::class);

        Route::apiResource('admin_menus', Admin\AdminMenuController::class);

        Route::put('admin_actions/sync', [Admin\AdminActionController::class, 'sync']);
        Route::apiResource('admin_actions', Admin\AdminActionController::class)->only('index', 'show', 'destroy');

    });
