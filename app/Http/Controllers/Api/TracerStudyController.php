<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TracerStudy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Import fasad Storage
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class TracerStudyController extends Controller
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
            $query = TracerStudy::with(['tahun'])
                ->where('tahun_id', $request->tahun_id);

// Ambil semua data dari query builder ke dalam bentuk array atau collection
            $data = $query->get()->toArray(); // atau ->get() jika ingin tetap sebagai Collection

// Buat paginasi manual pada koleksi lembagaList
            $offset = ($page - 1) * $size;
            $itemsForCurrentPage = array_slice($data, $offset, $size); // Gunakan $data (array) di sini
            $paginatedData = new LengthAwarePaginator(
                $itemsForCurrentPage,
                count($data),
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
                'report_file' => 'required|file|mimes:pdf,xls,xlsx,doc,docx|max:10240', // Maksimal 10MB
            ]);

            // 2. Pengecekan duplikasi sebelum mengunggah
            $existingRecord = TracerStudy::where('tahun_id', $request->tahun_id)
                ->where('jenjang', $request->jenjang)
                ->first();

            if ($existingRecord) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Laporan dengan kombinasi tahun dan jenjang yang sama sudah ada.',
                ], 409); // 409 Conflict
            }

            // 3. Simpan file yang diunggah
            $file = $request->file('report_file');
            $originalName = $file->getClientOriginalName();
            $path = $file->store('tracer-study', 'public');

            // 4. Simpan data ke database dalam transaksi
            DB::beginTransaction();
            $laporan = TracerStudy::create([
                'tahun_id' => $request->tahun_id,
                'jenjang' => $request->jenjang,
                'name_file' => $originalName,
                'path' => $path,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'BKSM berhasil diunggah dan disimpan.',
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
    public function show(TracerStudy $tracerStudy)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TracerStudy $tracerStudy)
    {
    }

    public function updateFile(Request $request, TracerStudy $tracerStudy)
    {
        try {
            // 1. Validasi input: hanya file yang diwajibkan
            $request->validate([
                'report_file' => 'required|file|mimes:pdf,xls,xlsx,doc,docx|max:10240', // Maksimal 10MB
            ]);
            // 2. Simpan data ke database dalam transaksi
            DB::beginTransaction();

            // 3. Hapus file lama jika ada
            if ($tracerStudy->path && Storage::disk('public')->exists($tracerStudy->path)) {
                Storage::disk('public')->delete($tracerStudy->path);
            }

            // 4. Unggah file baru
            $file = $request->file('report_file');
            $originalName = $file->getClientOriginalName();
            $path = $file->store('tracer-study', 'public');

            // 5. Perbarui entri database
            $tracerStudy->update([
                'name_file' => $originalName,
                'path' => $path,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'File laporan berhasil diperbarui.',
                'data' => $tracerStudy,
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
    public function destroy(TracerStudy $tracerStudy)
    {
        try {
            // Cari laporan berdasarkan ID
            // Hapus file fisik dari penyimpanan
            if ($tracerStudy->path && Storage::disk('public')->exists($tracerStudy->path)) {
                Storage::disk('public')->delete($tracerStudy->path);
            }

            // Hapus entri dari database
            $tracerStudy->update([
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

    public function download(TracerStudy $tracerStudy)
    {
        try {
            // 1. Cari data laporan berdasarkan ID
            $filePath = $tracerStudy->path;
            $fileName = $tracerStudy->name_file;

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
