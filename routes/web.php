<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\BrandController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();

    Route::post('/import', [CarController::class, "import"])->name("import_parse");
    Route::get('/export', [CarController::class, "export"]);
    Route::get('/brands-import', [BrandController::class, "store"]);
});
