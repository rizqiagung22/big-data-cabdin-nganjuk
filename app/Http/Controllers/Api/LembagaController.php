<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lembaga;
use Illuminate\Http\Request;

class LembagaController extends Controller
{
    public function __construct()
    {
//        $this->middleware('auth:sanctum');
//        $this->middleware('can:is-super-admin')->only(['index']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lembaga = Lembaga::all();
        return response()->json([
            'message' => 'Daftar lembaga berhasil diambil.',
            'data' => $lembaga
        ]);
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
    public function show(Lembaga $lembaga)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lembaga $lembaga)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lembaga $lembaga)
    {
        //
    }
}
