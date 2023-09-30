<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// vehicle
Route::get('/vehicle/registers', [VehicleController::class, 'index'])->name('vehicle-register');
Route::get('/vehicle/registers/{registerId}', [VehicleController::class, 'show']);
Route::post('/vehicle/registers', [VehicleController::class, 'store']);

// card
Route::get('/cards', [CardController::class, 'index']);
Route::get('/cards/{cardId}', [CardController::class, 'show']);
Route::post('/cards', [CardController::class, 'store']);


Route::group(['middleware' => ['auth:sanctum']], function () {
    //vehicle
    Route::patch('/vehicle/registers/{registerId}', [VehicleController::class, 'update']);
    Route::patch('/vehicle/registers/isapprove/{registerId}', [VehicleController::class, 'updateIsAprrove']);
    Route::delete('/vehicle/registers/{registerId}', [VehicleController::class, 'destroy']);

    //card
    Route::patch('/cards/{cardId}', [CardController::class, 'updateIsAprrove']);
    Route::delete('/cards/{cardId}', [CardController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
