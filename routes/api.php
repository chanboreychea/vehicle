<?php

use App\Http\Controllers\AuthController;
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

Route::post('/login', [AuthController::class, 'login']);

Route::get('/vehicle/registers', [VehicleController::class, 'index'])->name('vehicle-register');
Route::post('/vehicle/registers', [VehicleController::class, 'store']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::patch('/vehicle/registers/{registerId}', [VehicleController::class, 'update']);
    Route::delete('/vehicle/registers/{registerId}', [VehicleController::class, 'destroy']);


    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
