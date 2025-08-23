<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaporanTahunan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Import fasad Storage

use Illuminate\Pagination\LengthAwarePaginator;
class LaporanTahunanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('can:is-super-admin-or-admin')->only(['index']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Validasi input: pastikan 'tahun_id' ada dan merupakan integer
            $request->validate([
                'tahun_id' => 'required|integer|exists:tahun,id',
                'size' => 'nullable|integer|min:1', // Validasi size (optional)
                'page' => 'nullable|integer|min:1', // Validasi page (optional)
            ]);

            // Dapatkan size dan page dari request, default 15
            $size = $request->input('size', 15);
            $page = $request->input('page', 1);

            // Mengambil semua data laporan tahunan untuk tahun yang dipilih
            $laporan = LaporanTahunan::with(['lembaga', 'tahun'])
                ->where('tahun_id', $request->tahun_id)
                ->get();

            // Mendefinisikan semua jenis laporan yang mungkin ada
            $jenisLaporan = ['pagu', 'rkas', 'usulan per bulan', 'realisasi', 'penyerapan tiap bulan'];

            // Mengelompokkan data laporan berdasarkan 'lembaga_id'
            $groupedByLembaga = $laporan->groupBy('lembaga_id');

            // Ambil informasi tahun ajaran dari salah satu item laporan
            $tahunAjaran = $laporan->first()->tahun->tahun ?? null;

            $lembagaList = [];

            if ($groupedByLembaga->isNotEmpty()) {
                // Memproses setiap kelompok lembaga di dalam tahun
                foreach ($groupedByLembaga as $lembagaItems) {
                    $lembaga = $lembagaItems->first()->lembaga; // Ambil data lembaga dari item pertama

                    // Siapkan array laporan dengan nilai default null
                    $laporan = array_fill_keys($jenisLaporan, null);

                    // Isi array laporan dengan data yang ditemukan
                    foreach ($lembagaItems as $item) {
                        $laporan[$item->jenis_laporan->value] = [
                            'id' => $item->id,
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
            // Validasi input
            $request->validate([
                'tahun_id' => 'required|integer|exists:tahun,id',
                'lembaga_id' => 'required|integer|exists:lembaga,id',
                'jenis_laporan' => 'required|in:pagu,rkas,usulan per bulan,realisasi,penyerapan tiap bulan',
                'file' => 'required|file|mimes:pdf|max:2048', // Pastikan hanya file PDF dan ukuran maks 2MB
            ]);

            // Dapatkan semua data dari request
            $tahunId = $request->input('tahun_id');
            $lembagaId = $request->input('lembaga_id');
            $jenisLaporan = $request->input('jenis_laporan');
            $file = $request->file('file');

            // Bangun jalur penyimpanan file
            $folderPath = 'laporan/' . $tahunId . '/' . $lembagaId;
            $fileName = $jenisLaporan . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs($folderPath, $fileName, 'public');

            // Menggunakan updateOrCreate untuk menangani batasan unik
            LaporanTahunan::updateOrCreate(
                [
                    'tahun_id' => $tahunId,
                    'lembaga_id' => $lembagaId,
                    'jenis_laporan' => $jenisLaporan
                ],
                [
                    'path' => $filePath
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'File berhasil diunggah dan data disimpan.',
                'path' => Storage::url($filePath),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengunggah file: ' . $e->getMessage(),
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
    public function update(Request $request, LaporanTahunan $laporanTahunan)
    {
        try {
            // Validasi input
            $request->validate([
                'file' => 'required|file|mimes:pdf|max:2048', // Hanya file yang dibutuhkan untuk update
            ]);

            // Cari laporan berdasarkan ID
            $laporan = LaporanTahunan::findOrFail($laporanTahunan);

            // Hapus file lama jika ada
            if (Storage::disk('public')->exists($laporan->path)) {
                Storage::disk('public')->delete($laporan->path);
            }

            // Dapatkan data file baru dari request
            $file = $request->file('file');
            $jenisLaporan = $laporan->jenis_laporan->value; // Ambil nilai enum dari model
            $tahunId = $laporan->tahun_id;
            $lembagaId = $laporan->lembaga_id;

            // Bangun jalur penyimpanan file baru
            $folderPath = 'laporan/' . $tahunId . '/' . $lembagaId;
            $fileName = $jenisLaporan . '.' . $file->getClientOriginalExtension();
            $newFilePath = $file->storeAs($folderPath, $fileName, 'public');

            // Perbarui jalur file di database
            $laporan->update([
                'path' => $newFilePath,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'File laporan berhasil diperbarui.',
                'path' => Storage::url($newFilePath),
            ]);

        } catch (\Exception $e) {
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
            $laporan = LaporanTahunan::findOrFail($laporanTahunan);
            // Hapus file fisik dari penyimpanan
            if ($laporan->path && Storage::disk('public')->exists($laporan->path)) {
                Storage::disk('public')->delete($laporan->path);
            }

            // Hapus entri dari database
            $laporan->delete();

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
}
