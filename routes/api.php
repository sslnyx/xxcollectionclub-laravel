<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\SayHelloController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GalleryController;


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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::apiResource("/cars", CarController::class);

Route::apiResource("/brands", BrandController::class);

Route::get("/cars/brand/{cat}", [CarController::class, "search"]);

Route::get("/search",[CarController::class, "advanceSearch"]);

Route::post("/sayhello", [SayHelloController::class, "store"]);

Route::get('/gallery', GalleryController::class);

