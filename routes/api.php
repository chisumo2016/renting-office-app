<?php

use App\Http\Controllers\HostReservationController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\OfficeImageController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserReservationController;
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
Route::get('offices',           [OfficeController::class, 'index']);
Route::get('/offices/{office}', [OfficeController::class, 'show']);
Route::post('/offices',         [OfficeController::class, 'create'])->middleware(['auth:sanctum','verified']);

Route::put('/offices/{office}',  [OfficeController::class, 'update'])->middleware(['auth:sanctum','verified']);

Route::delete('/offices/{office}', [OfficeController::class, 'delete'])->middleware(['auth:sanctum','verified']);

//office Photos
Route::post('/offices/{office}/images',            [OfficeImageController::class, 'store'])->middleware(['auth:sanctum','verified']);
Route::delete('/offices/{office}/images/{image:id}',  [OfficeImageController::class, 'delete'])->middleware(['auth:sanctum','verified']);

//User Reservations
Route::get('/reservations',           [UserReservationController::class, 'index'])->middleware(['auth:sanctum','verified']);
Route::post('/reservations',          [UserReservationController::class, 'create'])->middleware(['auth:sanctum','verified']);
Route::delete('/reservations/{reservation}',          [UserReservationController::class, 'cancel'])->middleware(['auth:sanctum','verified']);

//Host Reservations
Route::get('/host/reservations',           [HostReservationController::class, 'index']);
