<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\UserController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
    Route::get('profile', [UserController::class, 'index'])->middleware('auth:api');
    Route::get('myproduk', [UserController::class, 'myproduk'])->middleware('auth:api');
});

Route::group(['prefix' => 'produk'], function () {
    Route::put('edit/{id}', [ProdukController::class, 'update'])->middleware('auth:api');
    Route::delete('delete/{id}', [ProdukController::class, 'destroy'])->middleware('auth:api');
    Route::post('create', [ProdukController::class, 'store'])->middleware('auth:api');
    Route::get('', [ProdukController::class, 'index']);
    Route::get('{id}', [ProdukController::class, 'show'])->middleware('auth:api');
});

Route::group(['prefix' => 'kategori'], function () {
    Route::put('edit/{id}', [KategoriController::class, 'update'])->middleware('auth:api');
    Route::delete('delete/{id}', [KategoriController::class, 'destroy'])->middleware('auth:api');
    Route::post('create', [KategoriController::class, 'store'])->middleware('auth:api');
    Route::get('', [KategoriController::class, 'index']);
    Route::get('{id}', [KategoriController::class, 'show'])->middleware('auth:api');
});
