<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\PingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('ping', [PingController::class, 'pingAction'])->middleware('check_header');

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth.jwt');
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth.jwt');
    Route::post('me', [AuthController::class, 'me'])->middleware('auth.jwt');
});

Route::group([
    'prefix' => 'book'
], function () {
    Route::get('/{slug}', [BookController::class, 'getBySlug'])->middleware('auth.jwt');
});



