<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct()
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
    public function login(Request $request): JsonResponse
    {
        // Validasi input email dan password
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Coba untuk mengautentikasi pengguna
        if (!Auth::attempt($request->only('email', 'password'))) {
            // Jika autentikasi gagal, lempar exception validasi
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')], // Menggunakan pesan default Laravel untuk login gagal
            ]);
        }

        // Dapatkan pengguna yang terautentikasi
        $user = Auth::user();
        // Hapus token yang ada untuk pengguna ini (opsional, tergantung kebutuhan)
        // $user->tokens()->delete();

        // Buat token API baru untuk pengguna
        // 'auth_token' adalah nama token, bisa disesuaikan
        $token = $user->createToken('auth_token')->plainTextToken;

        // Kembalikan respons JSON dengan token dan data pengguna
        return response()->json([
            'message' => 'Login berhasil!',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function getUser(Request $request)
    {
        // Opsi 1: Menggunakan Auth Facade
        $token = $request->bearerToken();

        // Menampilkan token dan menghentikan eksekusi
        $user = Auth::user();

        // Opsi 2: Menggunakan instance Request
        // $user = $request->user();

        if ($user) {
            return response()->json([
                'message' => 'User data retrieved successfully',
                'user' => $user
            ]);
        }

        return response()->json(['message' => $token], 401);
    }
}
