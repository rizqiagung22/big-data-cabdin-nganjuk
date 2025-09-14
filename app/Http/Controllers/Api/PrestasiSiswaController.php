<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrestasiSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Import fasad Storage
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class PrestasiSiswaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('can:is-super-admin-or-admin')->only(['store', 'updateFile']);
    }

    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'search' => 'nullable|string',
                'tahun_id' => 'required|integer|exists:tahun,id',
                'size' => 'nullable|integer|min:1',
                'page' => 'nullable|integer|min:1',
            ]);

            // Dapatkan size dan page dari request
            $size = $request->input('size', 15);
            $page = $request->input('page', 1);

            // Mengambil semua data laporan tahunan untuk tahun yang dipilih dengan filter pencarian
            $prestasi = PrestasiSiswa::where('tahun_id', $request->tahun_id)->get();

// Buat array kosong untuk menampung hasil yang diinginkan
            $groupedData = [];
            $totals = [];

// Loop setiap data untuk mengelompokkannya berdasarkan 'jenjang'
            foreach ($prestasi as $item) {
                $jenjang = $item->jenjang;

                // Jika 'jenjang' belum ada di array, inisialisasi
                if (!isset($groupedData[$jenjang])) {
                    $groupedData[$jenjang] = [
                        'jenjang' => $jenjang,
                        'total' => 0,
                    ];
                }

                // Tambahkan data ke dalam grup yang sesuai
                $groupedData[$jenjang][strtolower($item->tingkat)] = [
                    'id' => $item->id,
                    'tingkat' => $item->tingkat,
                    'tahun_id' => $item->tahun_id,
                    'name_file' => $item->name_file,
                    'path' => $item->path,
                ];

                // Hitung total untuk setiap jenjang
                $groupedData[$jenjang]['total']++;
            }

// Buat paginasi manual pada koleksi lembagaList
            $offset = ($page - 1) * $size;
            $itemsForCurrentPage = array_slice(array_values($groupedData), $offset, $size); // Gunakan $data (array) di sini
            $paginatedData = new LengthAwarePaginator(
                $itemsForCurrentPage,
                count($groupedData),
                $size,
                $page,
                [
                    'path' => $request->url(),
                    'query' => $request->query()
                ]
            );
            // Gabungkan metadata pagination dengan data yang sudah diformat
            $response['message'] = 'Data laporan tahunan berhasil diambil.';
            $response['status'] = 'success';
            $response['data'] = $paginatedData->toArray();

            return response()->json($response);

        } catch (\Exception $e) {
            // Tangani kesalahan yang mungkin terjadi
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // 1. Validasi input dari request
            $request->validate([
                'tahun_id' => 'required|integer|exists:tahun,id',
                'jenjang' => 'required|string|in:SMA,SMK,SLB',
                'tingkat' => 'required|string|in:Kecamatan,Kabupaten,Provinsi,Nasional,Internasional',
                'report_file' => 'required|file|mimes:pdf,xls,xlsx,doc,docx|max:10240', // Maksimal 10MB
            ]);

            // 2. Pengecekan duplikasi sebelum mengunggah
            $existingRecord = PrestasiSiswa::where('tahun_id', $request->tahun_id)
                ->where('jenjang', $request->jenjang)
                ->where('tingkat', $request->tingkat)
                ->first();

            if ($existingRecord) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Laporan dengan kombinasi tahun, jenjang, dan tingkat yang sama sudah ada.',
                ], 409); // 409 Conflict
            }

            // 3. Simpan file yang diunggah
            $file = $request->file('report_file');
            $originalName = $file->getClientOriginalName();
            $path = $file->store('prestasi-siswa', 'public');

            // 4. Simpan data ke database dalam transaksi
            DB::beginTransaction();
            $laporan = PrestasiSiswa::create([
                'tahun_id' => $request->tahun_id,
                'jenjang' => $request->jenjang,
                'tingkat' => $request->tingkat,
                'name_file' => $originalName,
                'path' => $path,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Prestasi siswa berhasil diunggah dan disimpan.',
                'data' => $laporan,
            ], 201); // 201 Created

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengunggah laporan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PrestasiSiswa $prestasiSiswa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PrestasiSiswa $prestasiSiswa)
    {
    }

    public function updateFile(Request $request, PrestasiSiswa $prestasiSiswa)
    {
        try {
            // 1. Validasi input: hanya file yang diwajibkan
            $request->validate([
                'report_file' => 'required|file|mimes:pdf,xls,xlsx,doc,docx|max:10240', // Maksimal 10MB
            ]);
            // 2. Simpan data ke database dalam transaksi
            DB::beginTransaction();

            // 3. Hapus file lama jika ada
            if ($prestasiSiswa->path && Storage::disk('public')->exists($prestasiSiswa->path)) {
                Storage::disk('public')->delete($prestasiSiswa->path);
            }

            // 4. Unggah file baru
            $file = $request->file('report_file');
            $originalName = $file->getClientOriginalName();
            $path = $file->store('prestasi-siswa', 'public');

            // 5. Perbarui entri database
            $prestasiSiswa->update([
                'name_file' => $originalName,
                'path' => $path,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'File laporan berhasil diperbarui.',
                'data' => $prestasiSiswa,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrestasiSiswa $prestasiSiswa)
    {
        try {
            // Cari laporan berdasarkan ID
            // Hapus file fisik dari penyimpanan
            if ($prestasiSiswa->path && Storage::disk('public')->exists($prestasiSiswa->path)) {
                Storage::disk('public')->delete($prestasiSiswa->path);
            }

            // Hapus entri dari database
            $prestasiSiswa->update([
                'name_file' => null,
                'path' => null,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Laporan berhasil dihapus.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus laporan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function download(PrestasiSiswa $prestasiSiswa)
    {
        try {
            // 1. Cari data laporan berdasarkan ID
            $filePath = $prestasiSiswa->path;
            $fileName = $prestasiSiswa->name_file;

            // 2. Cek apakah file benar-benar ada di storage
            if (!Storage::disk('public')->exists($filePath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File tidak ditemukan di server.',
                ], 404); // 404 Not Found
            }

            // 3. Kirim file sebagai response
            return Storage::disk('public')->download($filePath, $fileName);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Tangani jika ID tidak ditemukan di database
            return response()->json([
                'status' => 'error',
                'message' => 'Laporan dengan ID tersebut tidak ditemukan.',
            ], 404);
        } catch (\Exception $e) {
            // Tangani kesalahan umum lainnya
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengunduh file: ' . $e->getMessage(),
            ], 500);
        }
    }
}
