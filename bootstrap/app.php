<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'api/login',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (Throwable $e, Request $request) { // Throwable di sini adalah type-hint, bukan use statement
            // Cek apakah ini adalah permintaan API
            // Anda bisa menyesuaikan kondisi ini, misalnya:
            // $request->is('api/*') || $request->is('school/api/*') || $request->expectsJson()
            if ($request->is('api/*') || $request->is(config('app.api_prefix', 'api').'/*') || $request->expectsJson()) {

                $statusCode = 500; // Default status code
                $response = [
                    'success' => false,
                    'message' => 'Terjadi kesalahan pada server.',
                ];
                $headers = []; // Default headers

                // Persiapkan exception (mirip dengan yang ada di Handler lama)
                if ($e instanceof \Illuminate\Http\Client\ConnectionException) {
                    // Khusus untuk ConnectionException, bisa jadi service lain mati
                    $statusCode = 503; // Service Unavailable
                    $response['message'] = 'Layanan eksternal tidak tersedia saat ini.';
                } elseif ($e instanceof \Illuminate\Http\Client\RequestException) {
                    // Jika request ke service lain gagal dengan status code tertentu
                    $statusCode = $e->response ? $e->response->status() : 502; // Bad Gateway atau status dari response
                    $response['message'] = 'Gagal berkomunikasi dengan layanan eksternal.';
                    if ($e->response && config('app.debug')) {
                        $response['external_error'] = $e->response->json() ?: $e->response->body();
                    }
                } elseif ($e instanceof HttpResponseException) {
                    return $e->getResponse();
                } elseif ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                    $statusCode = 404;
                    $response['message'] = 'Resource yang diminta tidak ditemukan.';
                } elseif ($e instanceof AuthenticationException) {
                    $statusCode = 401;
                    $response['message'] = $e->getMessage() ?: 'Tidak terautentikasi.';
                } elseif ($e instanceof ValidationException) {
                    $statusCode = 422;
                    $response['message'] = $e->getMessage() ?: 'Data yang diberikan tidak valid.';
                    $response['errors'] = $e->errors();
                } elseif ($e instanceof HttpException) {
                    $statusCode = $e->getStatusCode();
                    $response['message'] = $e->getMessage() ?: ('Error HTTP ' . $statusCode);
                    // Ambil headers dari HttpException jika ada
                    $headers = $e->getHeaders();
                } else {
                    // Untuk exception umum lainnya
                    $response['message'] = config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan pada server.';
                }
                return response()->json($response, $statusCode, $headers);
            }
        });

    })->create();
