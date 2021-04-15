<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\v1\TradesController;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::resource('trades', TradesController::class);


Route::get('/active-list', [App\Http\Controllers\Api\v1\ManageController::class, 'index']);
Route::get('/active', [App\Http\Controllers\Api\v1\ManageController::class, 'active']);
Route::get('/in-active', [App\Http\Controllers\Api\v1\ManageController::class, 'inActive']);
Route::get('/swap-active', [App\Http\Controllers\Api\v1\ManageController::class, 'swapActive']);
Route::get('/swap-in-active', [App\Http\Controllers\Api\v1\ManageController::class, 'swapInActive']);
