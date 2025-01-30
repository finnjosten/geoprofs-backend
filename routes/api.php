<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BalanceController;
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


Route::group(['middleware' => 'auth:sanctum'], function () {
    // Allow creating users without auth when app is in local mode
    Route::get      ('user/',                           [UserController::class, 'showCurrent']  )->name('users.self');
    Route::get      ('users/',                          [UserController::class, 'index']        )->name('users.index');
    Route::post     ('users/create',                    [UserController::class, 'store']        )->name('users.create');
    Route::get      ('users/{id}',                      [UserController::class, 'show']         )->name('users.show');
    Route::post     ('users/{id}/update',               [UserController::class, 'update']       )->name('users.update');
    Route::match    (['DELETE','POST'],'users/{id}/delete',[UserController::class, 'destroy']   )->name('users.delete');

    Route::get      ('roles/',                          [RoleController::class, 'index']        )->name('roles.index');

    Route::get      ('agenda/get/{slug?}',              [AgendaController::class, 'show']       )->name('agenda.show');
    Route::get      ('agenda/generate',                 [AgendaController::class, 'generate']   )->name('agenda.generate');

    Route::get      ('attendance/',                     [AttendanceController::class, 'index']  )->name('atendance.index');
    Route::post     ('attendance/create',               [AttendanceController::class, 'store']  )->name('atendance.store');
    Route::get      ('attendance/{id}',                 [AttendanceController::class, 'show']   )->name('atendance.show');
    Route::post     ('attendance/{id}/update',          [AttendanceController::class, 'update'] )->name('atendance.update');
    Route::match    (['DELETE','POST'],'attendance/{id}/delete',[AttendanceController::class,'destroy'])->name('atendance.delete');

    Route::post     ('attendance/{id}/approve',         [AttendanceController::class, 'approve']    )->name('atendance.approve');
    Route::post     ('attendance/{id}/deny',            [AttendanceController::class, 'deny']       )->name('atendance.deny');

    Route::get      ('attendance-status/',              [AttendanceStatusController::class, 'index']    )->name('attendance.status.index');
    Route::post     ('attendance-status/create',        [AttendanceStatusController::class, 'store']    )->name('attendance.status.store');
    Route::get      ('attendance-status/{slug}',        [AttendanceStatusController::class, 'show']     )->name('attendance.status.show');
    Route::post     ('attendance-status/{slug}/update', [AttendanceStatusController::class, 'update']   )->name('attendance.status.update');
    Route::match    (['DELETE','POST'],'attendance-status/{slug}/delete', [AttendanceStatusController::class, 'destroy']  )->name('attendance.status.delete');

    Route::get      ('attendance-reason/',              [AttendanceReasonController::class, 'index']    )->name('attendance.reason.index');
    Route::post     ('attendance-reason/create',        [AttendanceReasonController::class, 'store']    )->name('attendance.reason.store');
    Route::get      ('attendance-reason/{slug}',        [AttendanceReasonController::class, 'show']     )->name('attendance.reason.show');
    Route::post     ('attendance-reason/{slug}/update', [AttendanceReasonController::class, 'update']   )->name('attendance.reason.update');
    Route::match    (['DELETE','POST'],'attendance-reason/{slug}/delete', [AttendanceReasonController::class, 'destroy']  )->name('attendance.reason.delete');

    Route::get      ('balance/{user:id?}',              [BalanceController::class, 'balance']       )->name('balance.index');
    Route::get      ('balance/{user:id?}/set',          [BalanceController::class, 'changeBalance'] )->name('balance.change');
});
