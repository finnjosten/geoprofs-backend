<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/', function () {
    abort(404);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login',               [AuthController::class, 'login']    )->name('auth.login');
    Route::post('/logout',              [AuthController::class, 'logout']   )->name('auth.logout');
});

Route::get('users/',                    [UserController::class, 'index']    )->name('users.index');
Route::post('users/create',             [UserController::class, 'create']   )->name('users.create');
Route::get('users/{user_id}',           [UserController::class, 'show']     )->name('users.show');
Route::post('users/{user_id}/update',   [UserController::class, 'update']   )->name('users.update');
Route::post('users/{user_id}/delete',   [UserController::class, 'delete']   )->name('users.delete');
