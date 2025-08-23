<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tahun;
use Illuminate\Http\Request;

class TahunController extends Controller
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
        $tahun = Tahun::all();
        return response()->json([
            'message' => 'Daftar tahun berhasil diambil.',
            'data' => $tahun
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
    public function show(Tahun $tahun)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tahun $tahun)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tahun $tahun)
    {
        //
    }
}
