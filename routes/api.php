<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */


Route::get('/', function () { abort(404); });

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login',   [AuthController::class, 'login']    )->name('login');
    Route::post('/logout',  [AuthController::class, 'logout']   )->name('logout')->middleware('auth:sanctum');
});

// Allow creating users without auth when app is in local mode
Route::post('users/create',[UserController::class, 'store'])->name('users.create');
if (env('APP_ENV') == 'production') Route::post('users/create',[UserController::class, 'store'])->name('users.create')->middleware('auth:sanctum');
Route::get      ('user/',                   [UserController::class, 'showCurrent']  )->name('users.index')->middleware('auth:sanctum');
Route::get      ('users/',                  [UserController::class, 'index']        )->name('users.index')->middleware('auth:sanctum');
Route::get      ('users/{id}',              [UserController::class, 'show']         )->name('users.show')->middleware('auth:sanctum');
Route::post     ('users/{id}/update',       [UserController::class, 'update']       )->name('users.update')->middleware('auth:sanctum');
Route::delete   ('users/{id}/delete',       [UserController::class, 'destroy']      )->name('users.delete')->middleware('auth:sanctum');
