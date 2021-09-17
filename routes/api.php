<?php

use App\Http\Controllers\OfficeController;
use App\Http\Controllers\TagController;
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
//Tags Endpoint
Route::get('/tags',TagController::class);

//office Endpoint
Route::get('offices',          [OfficeController::class, 'index']);
Route::get('/offices/{offices}',[OfficeController::class, 'show']);

