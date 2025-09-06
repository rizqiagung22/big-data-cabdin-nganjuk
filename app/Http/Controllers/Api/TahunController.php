<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lembaga;
use App\Models\Tahun;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
class TahunController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('can:is-super-admin')->only(['store']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Validasi parameter
        $request->validate([
            'size' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1',
        ]);

        // 2. Buat query dasar yang sudah diurutkan
        $query = Tahun::orderBy('tahun', 'desc');
        $response = [
            'message' => 'Daftar tahun berhasil diambil.',
        ];

        // 3. Cek keberadaan parameter 'size'
        if ($request->has('size')) {
            // Jika 'size' ada, lakukan pagination
            $size = $request->input('size', 15);

            // 3. Ambil data tahun dengan urutan dan pagination
            $tahuns = Tahun::orderBy('tahun', 'desc')->paginate($size);

            // 4. Kembalikan respons JSON
            return response()->json([
                'tahun_ajaran' => '2023/2024', // Sesuaikan dengan logika Anda untuk mendapatkan tahun ajaran
                'message' => 'Daftar tahun berhasil diambil.',
                'status' => 'success',
                'data' => $tahuns->toArray(),
            ]);
        } else {
            // Jika 'size' tidak ada, ambil semua data
            $tahuns = $query->get();

            // Format data tanpa metadata pagination
            $response['data'] = $tahuns->map(function ($tahun) {
                return [
                    'id' => $tahun->id,
                    'tahun' => $tahun->tahun,
                ];
            });
        }

        // 4. Kembalikan respons JSON
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        // 1. Validasi input tahun
        $request->validate([
            'tahun' => 'required|integer|unique:tahun',
        ]);

        // 2. Mulai transaksi
        DB::beginTransaction();

        try {
            // 3. Insert tahun ke db tahun & dapatkan id
            $tahun = Tahun::create([
                'tahun' => $request->tahun,
            ]);
            $tahunId = $tahun->id;

            // 4. Get semua lembaga id dari table lembaga
            $lembagaIds = Lembaga::pluck('id');

            $data = [];
            $jenisLaporan = ['pagu', 'rkas', 'usulan per bulan', 'realisasi', 'penyerapan tiap bulan'];

            foreach ($lembagaIds as $lembagaId) {
                foreach ($jenisLaporan as $jenis) {
                    $data[] = [
                        'tahun_id' => $tahunId,
                        'lembaga_id' => $lembagaId,
                        'jenis_laporan' => $jenis,
                        'path' => 'laporan/' . str_replace(' ', '_', $jenis) . '/lembaga_' . $lembagaId . '_tahun_' . $tahunId . '.pdf',
                        'name_file' => 'lembaga_' . $lembagaId . '_tahun_' . $tahunId . '.pdf',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }

            // 5. Lakukan batch insert ke database
            DB::table('bpopp')->insert($data);

            // 6. Jika semua operasi berhasil, commit transaksi
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data laporan berhasil dibuat.',
                'data' => [
                    'tahun_id' => $tahunId,
                    'laporan_dibuat' => count($data)
                ]
            ], 201);

        } catch (\Exception $e) {
            // 8. Jika terjadi kesalahan, rollback transaksi
            DB::rollBack();

            // 9. Kembalikan respons JSON error
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat laporan.',
                'error' => $e->getMessage()
            ], 500);
       }
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
        $request->validate([
            'tahun' => 'required|integer|unique:tahun',
        ]);

        try {
            // 2. Perbarui data di database
            $tahun->update([
                'tahun' => $request->tahun,
            ]);

            // 3. Kembalikan respons JSON sukses
            return response()->json([
                'status' => 'success',
                'message' => 'Data tahun berhasil diperbarui.',
                'data' => $tahun,
            ]);

        } catch (\Exception $e) {
            // 4. Tangani kesalahan dan kembalikan respons JSON error
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui data tahun.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tahun $tahun)
    {
        DB::beginTransaction();

        try {
            // 2. Hapus data tahun
            $tahun->delete();

            // 3. Commit transaksi jika berhasil
            DB::commit();

            // 4. Kembalikan respons JSON sukses
            return response()->json([
                'status' => 'success',
                'message' => 'Data tahun berhasil dihapus.'
            ]);

        } catch (\Exception $e) {
            // 5. Rollback transaksi jika terjadi kesalahan
            DB::rollBack();

            // 6. Kembalikan respons JSON error
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data tahun.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
