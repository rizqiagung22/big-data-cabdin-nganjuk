<?php

use Illuminate\Support\Facades\Route;

const BPOPP_ROUTE = 'api/bpopp';
Route::post('api/login', [App\Http\Controllers\Api\AuthController::class, 'login'])->name('login');


Route::get('api/tahun', [App\Http\Controllers\Api\TahunController::class, 'index']);
Route::get('api/lembaga', [App\Http\Controllers\Api\LembagaController::class, 'index']);

Route::get(BPOPP_ROUTE, [App\Http\Controllers\Api\LaporanTahunanController::class, 'index']);
Route::post(BPOPP_ROUTE, [App\Http\Controllers\Api\LaporanTahunanController::class, 'store']);
Route::put(BPOPP_ROUTE . '/{laporanTahunan}', [App\Http\Controllers\Api\LaporanTahunanController::class, 'update']);
Route::delete(BPOPP_ROUTE . '/{laporanTahunan}', [App\Http\Controllers\Api\LaporanTahunanController::class, 'destroy']);


Route::get('api/user', [App\Http\Controllers\Api\AuthController::class, 'getUser'])->middleware('auth:sanctum');

Route::get('/{any}', function () {
    // Arahkan semua permintaan ke file index.html dari hasil build Vue
    return file_get_contents(public_path('index.html'));
})->where('any', '.*');
