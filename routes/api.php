<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceStatusController;
use App\Http\Controllers\AttendanceReasonController;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */


Route::get('/', function () { abort(404); });

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login',   [AuthController::class, 'login']    )->name('login');
    Route::post('/logout',  [AuthController::class, 'logout']   )->name('logout')->middleware('auth:sanctum');
    Route::post('/testing', [AuthController::class, 'testing']  )->name('testing');
    Route::post('/testing-logout', [AuthController::class, 'testing_logout']  )->name('testing');
});

// Allow creating users without auth when app is in local mode
Route::get      ('user/',                           [UserController::class, 'showCurrent']  )->name('users.self')->middleware('auth:sanctum');
Route::get      ('users/',                          [UserController::class, 'index']        )->name('users.index')->middleware('auth:sanctum');
Route::post     ('users/create',                    [UserController::class, 'store']        )->name('users.create')->middleware('auth:sanctum');
Route::get      ('users/{id}',                      [UserController::class, 'show']         )->name('users.show')->middleware('auth:sanctum');
Route::post     ('users/{id}/update',               [UserController::class, 'update']       )->name('users.update')->middleware('auth:sanctum');
Route::match    (['DELETE','POST'],'users/{id}/delete',[UserController::class, 'destroy']   )->name('users.delete')->middleware('auth:sanctum');

Route::get      ('agenda/get/{slug?}',              [AgendaController::class, 'show']       )->name('agenda.show')->middleware('auth:sanctum');
Route::get      ('agenda/generate',                 [AgendaController::class, 'generate']   )->name('agenda.generate')->middleware('auth:sanctum');

Route::get      ('attendance/',                     [AttendanceController::class, 'index']  )->name('atendance.index')->middleware('auth:sanctum');
Route::post     ('attendance/create',               [AttendanceController::class, 'store']  )->name('atendance.store')->middleware('auth:sanctum');
Route::get      ('attendance/{id}',                 [AttendanceController::class, 'show']   )->name('atendance.show')->middleware('auth:sanctum');
Route::post     ('attendance/{id}/update',          [AttendanceController::class, 'update'] )->name('atendance.update')->middleware('auth:sanctum');
Route::match    (['DELETE','POST'],'attendance/{id}/delete',[AttendanceController::class,'destroy'])->name('atendance.delete')->middleware('auth:sanctum');

Route::post     ('attendance/{id}/approve',         [AttendanceController::class, 'approve']    )->name('atendance.approve')->middleware('auth:sanctum');
Route::post     ('attendance/{id}/deny',            [AttendanceController::class, 'deny']       )->name('atendance.deny')->middleware('auth:sanctum');

Route::get      ('attendance-status/',              [AttendanceStatusController::class, 'index']    )->name('attendance.status.index')->middleware('auth:sanctum');
Route::post     ('attendance-status/create',        [AttendanceStatusController::class, 'store']    )->name('attendance.status.store')->middleware('auth:sanctum');
Route::get      ('attendance-status/{slug}',        [AttendanceStatusController::class, 'show']     )->name('attendance.status.show')->middleware('auth:sanctum');
Route::post     ('attendance-status/{slug}/update', [AttendanceStatusController::class, 'update']   )->name('attendance.status.update')->middleware('auth:sanctum');
Route::match    (['DELETE','POST'],'attendance-status/{slug}/delete', [AttendanceStatusController::class, 'destroy']  )->name('attendance.status.delete')->middleware('auth:sanctum');

Route::get      ('attendance-reason/',              [AttendanceReasonController::class, 'index']    )->name('attendance.reason.index')->middleware('auth:sanctum');
Route::post     ('attendance-reason/create',        [AttendanceReasonController::class, 'store']    )->name('attendance.reason.store')->middleware('auth:sanctum');
Route::get      ('attendance-reason/{slug}',        [AttendanceReasonController::class, 'show']     )->name('attendance.reason.show')->middleware('auth:sanctum');
Route::post     ('attendance-reason/{slug}/update', [AttendanceReasonController::class, 'update']   )->name('attendance.reason.update')->middleware('auth:sanctum');
Route::match    (['DELETE','POST'],'attendance-reason/{slug}/delete', [AttendanceReasonController::class, 'destroy']  )->name('attendance.reason.delete')->middleware('auth:sanctum');
