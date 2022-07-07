<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;

Route::put('auth/login', [Admin\AuthController::class, 'login']);
Route::put('auth/logout', [Admin\AuthController::class, 'logout']);

Route::middleware(['auth:sanctum', 'admin.permission', 'admin.operation_log'])
    ->group(function () {

        Route::get('auth/user', [Admin\AuthController::class, 'user']);
        Route::put('auth/update', [Admin\AuthController::class, 'update']);
        Route::put('auth/password', [Admin\AuthController::class, 'password']);
        Route::get('auth/pages', [Admin\AuthController::class, 'pages']);
        Route::get('auth/menus', [Admin\AuthController::class, 'menus']);
        Route::get('auth/permissions', [Admin\AuthController::class, 'permissions']);
        Route::get('auth/logs', [Admin\AuthController::class, 'logs']);

        Route::put('admin_users/{admin_user}/permit', [Admin\AdminUserController::class, 'permit']);
        Route::put('admin_users/{admin_user}/assign', [Admin\AdminUserController::class, 'assign']);
        Route::get('admin_users/{admin_user}/permissions', [Admin\AdminUserController::class, 'permissions']);
        Route::apiResource('admin_users', Admin\AdminUserController::class);

        Route::put('admin_roles/{admin_role}/permit', [Admin\AdminRoleController::class, 'permit']);
        Route::get('admin_roles/{admin_role}/permissions', [Admin\AdminRoleController::class, 'permissions']);
        Route::apiResource('admin_roles', Admin\AdminRoleController::class);

        Route::apiResource('admin_pages', Admin\AdminPageController::class);

        Route::apiResource('admin_menus', Admin\AdminMenuController::class);

        Route::put('admin_routes/sync', [Admin\AdminRouteController::class, 'sync']);
        Route::apiResource('admin_routes', Admin\AdminRouteController::class)->only('index', 'show', 'destroy');

        Route::apiResource('admin_logs', Admin\AdminLogController::class)->only('index', 'show');
    });
