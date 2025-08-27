<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaporanTahunan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Import fasad Storage
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
class LaporanTahunanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('can:is-super-admin-or-user')->only(['download']);
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
            $query = LaporanTahunan::with(['lembaga', 'tahun'])
                ->where('tahun_id', $request->tahun_id);

            // Menambahkan kondisi pencarian jika 'search' ada
            if ($request->has('search')) {
                $query->whereHas('lembaga', function ($q) use ($request) {
                    $q->where('nama_satuan_pendidikan', 'like', '%' . $request->search . '%');
                });
            }

            $laporan = $query->get();

            // Mengelompokkan data laporan berdasarkan 'lembaga_id'
            $groupedByLembaga = $laporan->groupBy('lembaga_id');

            // Mengambil informasi tahun ajaran dari salah satu item laporan
            $tahunAjaran = $laporan->first()->tahun->tahun ?? null;

            $lembagaList = [];

            if ($groupedByLembaga->isNotEmpty()) {
                $jenisLaporan = ['pagu', 'rkas', 'usulan per bulan', 'realisasi', 'penyerapan tiap bulan'];

                // Memproses setiap kelompok lembaga di dalam tahun
                foreach ($groupedByLembaga as $lembagaItems) {
                    $lembaga = $lembagaItems->first()->lembaga;

                    // Siapkan array laporan dengan nilai default null
                    $laporan = array_fill_keys($jenisLaporan, null);

                    // Isi array laporan dengan data yang ditemukan
                    foreach ($lembagaItems as $item) {
                        $laporan[$item->jenis_laporan->value] = [
                            'id' => $item->id,
                            'tahun_id' => $item->tahun_id,
                            'name_file' => $item->name_file,
                            'path' => $item->path,
                        ];
                    }

                    $lembagaList[] = [
                        'lembaga' => [
                            'id' => $lembaga->id,
                            'nama' => $lembaga->nama_satuan_pendidikan,
                            'npsn' => $lembaga->npsn,
                            'bentuk_pendidikan' => $lembaga->bentuk_pendidikan,
                            'status_sekolah' => $lembaga->status_sekolah,
                        ],
                        'laporan' => $laporan,
                    ];
                }
            }

            // Buat paginasi manual pada koleksi lembagaList
            $offset = ($page - 1) * $size;
            $itemsForCurrentPage = array_slice($lembagaList, $offset, $size);
            $paginatedData = new LengthAwarePaginator(
                $itemsForCurrentPage,
                count($lembagaList),
                $size,
                $page,
                [
                    'path' => $request->url(),
                    'query' => $request->query()
                ]
            );

            // Gabungkan metadata pagination dengan data yang sudah diformat
            $response['tahun_ajaran'] = $tahunAjaran;
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
                'lembaga_id' => 'required|integer|exists:lembaga,id',
                'jenis_laporan' => 'required|string|in:pagu,rkas,usulan per bulan,realisasi,penyerapan tiap bulan',
                'report_file' => 'required|file|mimes:pdf,xls,xlsx,doc,docx|max:10240', // Maksimal 10MB
            ]);

            // 2. Pengecekan duplikasi sebelum mengunggah
            $existingRecord = LaporanTahunan::where('tahun_id', $request->tahun_id)
                ->where('lembaga_id', $request->lembaga_id)
                ->where('jenis_laporan', $request->jenis_laporan)
                ->first();

            if ($existingRecord) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Laporan dengan kombinasi tahun, lembaga, dan jenis yang sama sudah ada.',
                ], 409); // 409 Conflict
            }

            // 3. Simpan file yang diunggah
            $file = $request->file('report_file');
            $originalName = $file->getClientOriginalName();
            $path = $file->store('laporan-tahunan', 'public');

            // 4. Simpan data ke database dalam transaksi
            DB::beginTransaction();
            $laporan = LaporanTahunan::create([
                'tahun_id' => $request->tahun_id,
                'lembaga_id' => $request->lembaga_id,
                'jenis_laporan' => $request->jenis_laporan,
                'name_file' => $originalName,
                'path' => $path,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Laporan berhasil diunggah dan disimpan.',
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
    public function show(LaporanTahunan $laporanTahunan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LaporanTahunan $laporanTahunan){}
    public function updateFile(Request $request, LaporanTahunan $laporanTahunan)
    {
        try {
            // 1. Validasi input: hanya file yang diwajibkan
            $request->validate([
                'report_file' => 'required|file|mimes:pdf,xls,xlsx,doc,docx|max:10240', // Maksimal 10MB
            ]);
            // 2. Simpan data ke database dalam transaksi
            DB::beginTransaction();

            // 3. Hapus file lama jika ada
            if ($laporanTahunan->path && Storage::disk('public')->exists($laporanTahunan->path)) {
                Storage::disk('public')->delete($laporanTahunan->path);
            }

            // 4. Unggah file baru
            $file = $request->file('report_file');
            $originalName = $file->getClientOriginalName();
            $path = $file->store('laporan-tahunan', 'public');

            // 5. Perbarui entri database
            $laporanTahunan->update([
                'name_file' => $originalName,
                'path' => $path,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'File laporan berhasil diperbarui.',
                'data' => $laporanTahunan,
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
    public function destroy(LaporanTahunan $laporanTahunan)
    {
        try {
            // Cari laporan berdasarkan ID
            // Hapus file fisik dari penyimpanan
            if ($laporanTahunan->path && Storage::disk('public')->exists($laporanTahunan->path)) {
                Storage::disk('public')->delete($laporanTahunan->path);
            }

            // Hapus entri dari database
            $laporanTahunan->delete();

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
    public function download(LaporanTahunan $laporanTahunan)
    {
        try {
            // 1. Cari data laporan berdasarkan ID
            $filePath = $laporanTahunan->path;
            $fileName = $laporanTahunan->name_file;

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
