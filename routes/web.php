<?php

use Illuminate\Support\Facades\Route;

const BPOPP_ROUTE = 'api/bpopp';
const BOS_ROUTE = 'api/bos';
const BKSM_ROUTE = 'api/bksm';
const BSM_ROUTE = 'api/bsm';
const PENDISTRIBUSIAN_IJAZAH_ROUTE = 'api/pendistribusian-ijazah';

const PRESTASI_SISWA_ROUTE = 'api/prestasi-siswa';
const TRACER_STUDY_ROUTE = 'api/tracer-study';


Route::post('api/login', [App\Http\Controllers\Api\AuthController::class, 'login'])->name('login');


Route::get('api/tahun', [App\Http\Controllers\Api\TahunController::class, 'index']);
Route::post('api/tahun', [App\Http\Controllers\Api\TahunController::class, 'store']);
Route::put('api/tahun/{tahun}', [App\Http\Controllers\Api\TahunController::class, 'update']);
Route::delete('api/tahun/{tahun}', [App\Http\Controllers\Api\TahunController::class, 'destroy']);

Route::get('api/lembaga', [App\Http\Controllers\Api\LembagaController::class, 'index']);

Route::get(BPOPP_ROUTE, [App\Http\Controllers\Api\LaporanTahunanController::class, 'index']);
Route::post(BPOPP_ROUTE, [App\Http\Controllers\Api\LaporanTahunanController::class, 'store']);
Route::delete(BPOPP_ROUTE . '/{laporanTahunan}', [App\Http\Controllers\Api\LaporanTahunanController::class, 'destroy']);
Route::post(BPOPP_ROUTE . '/update-file/{laporanTahunan}', [App\Http\Controllers\Api\LaporanTahunanController::class, 'updateFile']);
Route::get(BPOPP_ROUTE . '/download/{laporanTahunan}', [App\Http\Controllers\Api\LaporanTahunanController::class, 'download']);

Route::get(BOS_ROUTE, [App\Http\Controllers\Api\BosController::class, 'index']);
Route::post(BOS_ROUTE, [App\Http\Controllers\Api\BosController::class, 'store']);
Route::delete(BOS_ROUTE . '/{bos}', [App\Http\Controllers\Api\BosController::class, 'destroy']);
Route::post(BOS_ROUTE . '/update-file/{bos}', [App\Http\Controllers\Api\BosController::class, 'updateFile']);
Route::get(BOS_ROUTE . '/download/{bos}', [App\Http\Controllers\Api\BosController::class, 'download']);

Route::get(BKSM_ROUTE, [App\Http\Controllers\Api\BksmController::class, 'index']);
Route::post(BKSM_ROUTE, [App\Http\Controllers\Api\BksmController::class, 'store']);
Route::delete(BKSM_ROUTE . '/{bksm}', [App\Http\Controllers\Api\BksmController::class, 'destroy']);
Route::post(BKSM_ROUTE . '/update-file/{bksm}', [App\Http\Controllers\Api\BksmController::class, 'updateFile']);
Route::get(BKSM_ROUTE . '/download/{bksm}', [App\Http\Controllers\Api\BksmController::class, 'download']);

Route::get(BSM_ROUTE, [App\Http\Controllers\Api\BsmController::class, 'index']);
Route::post(BSM_ROUTE, [App\Http\Controllers\Api\BsmController::class, 'store']);
Route::delete(BSM_ROUTE . '/{bsm}', [App\Http\Controllers\Api\BsmController::class, 'destroy']);
Route::post(BSM_ROUTE . '/update-file/{bsm}', [App\Http\Controllers\Api\BsmController::class, 'updateFile']);
Route::get(BSM_ROUTE . '/download/{bsm}', [App\Http\Controllers\Api\BsmController::class, 'download']);

Route::get(PENDISTRIBUSIAN_IJAZAH_ROUTE, [App\Http\Controllers\Api\PendistribusianIjazahController::class, 'index']);
Route::post(PENDISTRIBUSIAN_IJAZAH_ROUTE, [App\Http\Controllers\Api\PendistribusianIjazahController::class, 'store']);
Route::delete(PENDISTRIBUSIAN_IJAZAH_ROUTE . '/{pendistribusianIjazah}', [App\Http\Controllers\Api\PendistribusianIjazahController::class, 'destroy']);
Route::post(PENDISTRIBUSIAN_IJAZAH_ROUTE . '/update-file/{pendistribusianIjazah}', [App\Http\Controllers\Api\PendistribusianIjazahController::class, 'updateFile']);
Route::get(PENDISTRIBUSIAN_IJAZAH_ROUTE . '/download/{pendistribusianIjazah}', [App\Http\Controllers\Api\PendistribusianIjazahController::class, 'download']);

Route::get(PRESTASI_SISWA_ROUTE, [App\Http\Controllers\Api\PrestasiSiswaController::class, 'index']);
Route::post(PRESTASI_SISWA_ROUTE, [App\Http\Controllers\Api\PrestasiSiswaController::class, 'store']);
Route::delete(PRESTASI_SISWA_ROUTE . '/{prestasiSiswa}', [App\Http\Controllers\Api\PrestasiSiswaController::class, 'destroy']);
Route::post(PRESTASI_SISWA_ROUTE . '/update-file/{prestasiSiswa}', [App\Http\Controllers\Api\PrestasiSiswaController::class, 'updateFile']);
Route::get(PRESTASI_SISWA_ROUTE . '/download/{prestasiSiswa}', [App\Http\Controllers\Api\PrestasiSiswaController::class, 'download']);

Route::get(TRACER_STUDY_ROUTE, [App\Http\Controllers\Api\TracerStudyController::class, 'index']);
Route::post(TRACER_STUDY_ROUTE, [App\Http\Controllers\Api\TracerStudyController::class, 'store']);
Route::delete(TRACER_STUDY_ROUTE . '/{tracerStudy}', [App\Http\Controllers\Api\TracerStudyController::class, 'destroy']);
Route::post(TRACER_STUDY_ROUTE . '/update-file/{tracerStudy}', [App\Http\Controllers\Api\TracerStudyController::class, 'updateFile']);
Route::get(TRACER_STUDY_ROUTE . '/download/{tracerStudy}', [App\Http\Controllers\Api\TracerStudyController::class, 'download']);

Route::get('api/user', [App\Http\Controllers\Api\AuthController::class, 'getUser'])->middleware('auth:sanctum');

Route::get('/{any}', function () {
    // Arahkan semua permintaan ke file index.html dari hasil build Vue
    return file_get_contents(public_path('index.html'));
})->where('any', '.*');
