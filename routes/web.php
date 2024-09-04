<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('main');
});

Route::group(['prefix' => 'api'], function () {
    Route::get('/', function () {
        abort(404);
    });

    Route::get('/users', [UserController::class, 'index']);
});


