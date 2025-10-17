<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetBelanja;
use App\Models\Bos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Import fasad Storage
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class AssetController extends Controller
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
            // Query utama
            $query = Asset::with(['tahun', 'belanjaModal1', 'belanjaModal2', 'belanjaModal3', 'belanjaModal4'])
                ->where('tahun_id', $request->tahun_id);

            // Paginasi langsung dari query builder (lebih efisien)
            $paginatedData = $query->paginate($size, ['*'], 'page', $page);

            // Ambil tahun ajaran dari salah satu record
            $tahunAjaran = optional($paginatedData->first()->tahun)->tahun ?? null;

            // Format data jadi lebih rapi (misal grouping per asset + daftar belanja modal)
            $formattedData = $paginatedData->getCollection()->map(function ($asset) {
                return [
                    'asset_id' => $asset->id,
                    'tahun' => $asset->tahun->tahun,
                    'belanja_modal' => $asset,
                ];
            });

            // Ganti isi koleksi paginated dengan data yang sudah diformat
            $paginatedData->setCollection($formattedData);

            // Response
            return response()->json([
                'status' => 'success',
                'message' => 'Data laporan tahunan berhasil diambil.',
                'tahun_ajaran' => $tahunAjaran,
                'data' => $paginatedData,
            ]);
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
            $existingRecord = Bos::where('tahun_id', $request->tahun_id)
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
            $laporan = Bos::create([
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
    public function show(Bos $bos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bos $bos)
    {
    }

    public function updateFile(Request $request, AssetBelanja $assetBelanja)
    {
        try {
            // 1. Validasi input: hanya file yang diwajibkan
            $request->validate([
                'report_file' => 'required|file|mimes:pdf,xls,xlsx,doc,docx|max:10240', // Maksimal 10MB
                'type' => 'required|string', // Maksimal 10MB
            ]);
            if ($request->type === 'bos') {
                // 2. Simpan data ke database dalam transaksi
                DB::beginTransaction();
                // 3. Hapus file lama jika ada
                if ($assetBelanja->path && Storage::disk('public')->exists($assetBelanja->path)) {
                    Storage::disk('public')->delete($assetBelanja->path);
                }
                // 4. Unggah file baru
                $file = $request->file('report_file');
                $originalName = $file->getClientOriginalName();
                $path = $file->store('asset', 'public');

                // 5. Perbarui entri database
                $assetBelanja->update([
                    'bos_name_file' => $originalName,
                    'bos_path' => $path,
                ]);
                DB::commit();
            } else if ($request->type === 'bpopp') {
                // 2. Simpan data ke database dalam transaksi
                DB::beginTransaction();
                // 3. Hapus file lama jika ada
                if ($assetBelanja->path && Storage::disk('public')->exists($assetBelanja->path)) {
                    Storage::disk('public')->delete($assetBelanja->path);
                }
                // 4. Unggah file baru
                $file = $request->file('report_file');
                $originalName = $file->getClientOriginalName();
                $path = $file->store('asset', 'public');

                // 5. Perbarui entri database
                $assetBelanja->update([
                    'bpopp_name_file' => $originalName,
                    'bpopp_path' => $path,
                ]);
                DB::commit();
            } else if ($request->type === 'bp') {
                // 2. Simpan data ke database dalam transaksi
                DB::beginTransaction();
                // 3. Hapus file lama jika ada
                if ($assetBelanja->path && Storage::disk('public')->exists($assetBelanja->path)) {
                    Storage::disk('public')->delete($assetBelanja->path);
                }
                // 4. Unggah file baru
                $file = $request->file('report_file');
                $originalName = $file->getClientOriginalName();
                $path = $file->store('asset', 'public');

                // 5. Perbarui entri database
                $assetBelanja->update([
                    'bp_name_file' => $originalName,
                    'bp_path' => $path,
                ]);
                DB::commit();
            } else if ($request->type === 'pm') {
                // 2. Simpan data ke database dalam transaksi
                DB::beginTransaction();
                // 3. Hapus file lama jika ada
                if ($assetBelanja->path && Storage::disk('public')->exists($assetBelanja->path)) {
                    Storage::disk('public')->delete($assetBelanja->path);
                }
                // 4. Unggah file baru
                $file = $request->file('report_file');
                $originalName = $file->getClientOriginalName();
                $path = $file->store('asset', 'public');

                // 5. Perbarui entri database
                $assetBelanja->update([
                    'pm_name_file' => $originalName,
                    'pm_path' => $path,
                ]);
                DB::commit();
            }
            return response()->json([
                'status' => 'success',
                'message' => 'File laporan berhasil diperbarui.',
                'data' => $assetBelanja,
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
    public function destroy(Request $request, AssetBelanja $assetBelanja)
    {
        try {
            $request->validate([
                'type' => 'required|string', // Maksimal 10MB
            ]);

            if ($request->type === 'bos') {
                // 2. Simpan data ke database dalam transaksi
                DB::beginTransaction();
                // 3. Hapus file lama jika ada
                if ($assetBelanja->path && Storage::disk('public')->exists($assetBelanja->path)) {
                    Storage::disk('public')->delete($assetBelanja->path);
                }
                // 5. Perbarui entri database
                $assetBelanja->update([
                    'bos_name_file' => null,
                    'bos_path' => null,
                ]);
                DB::commit();
            } else if ($request->type === 'bpopp') {
                // 2. Simpan data ke database dalam transaksi
                DB::beginTransaction();
                // 3. Hapus file lama jika ada
                if ($assetBelanja->path && Storage::disk('public')->exists($assetBelanja->path)) {
                    Storage::disk('public')->delete($assetBelanja->path);
                }

                // 5. Perbarui entri database
                $assetBelanja->update([
                    'bpopp_name_file' => null,
                    'bpopp_path' => null,
                ]);
                DB::commit();
            } else if ($request->type === 'bp') {
                // 2. Simpan data ke database dalam transaksi
                DB::beginTransaction();
                // 3. Hapus file lama jika ada
                if ($assetBelanja->path && Storage::disk('public')->exists($assetBelanja->path)) {
                    Storage::disk('public')->delete($assetBelanja->path);
                }

                // 5. Perbarui entri database
                $assetBelanja->update([
                    'bp_name_file' => null,
                    'bp_path' => null,
                ]);
                DB::commit();
            } else if ($request->type === 'pm') {
                // 2. Simpan data ke database dalam transaksi
                DB::beginTransaction();
                // 3. Hapus file lama jika ada
                if ($assetBelanja->path && Storage::disk('public')->exists($assetBelanja->path)) {
                    Storage::disk('public')->delete($assetBelanja->path);
                }

                // 5. Perbarui entri database
                $assetBelanja->update([
                    'pm_name_file' => null,
                    'pm_path' => null,
                ]);
                DB::commit();
            }
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

    public function download(Request $request, AssetBelanja $assetBelanja)
    {
        try {
            $request->validate([
                'type' => 'required|string', // Maksimal 10MB
            ]);
            // 1. Cari data laporan berdasarkan ID
            if ($request->type === 'bos') {
                $fileName = $assetBelanja->bos_name_file;
                $filePath = $assetBelanja->bos_path;
                if (!Storage::disk('public')->exists($filePath)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'File tidak ditemukan di server.',
                    ], 404); // 404 Not Found
                }
                // 3. Kirim file sebagai response
                return Storage::disk('public')->download($filePath, $fileName);
            } else if ($request->type === 'bpopp') {
                $fileName = $assetBelanja->bpopp_name_file;
                $filePath = $assetBelanja->bpopp_path;
                if (!Storage::disk('public')->exists($filePath)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'File tidak ditemukan di server.',
                    ], 404); // 404 Not Found
                }
                // 3. Kirim file sebagai response
                return Storage::disk('public')->download($filePath, $fileName);
            } else if ($request->type === 'bp') {
                $fileName = $assetBelanja->bp_name_file;
                $filePath = $assetBelanja->bp_path;
                if (!Storage::disk('public')->exists($filePath)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'File tidak ditemukan di server.',
                    ], 404); // 404 Not Found
                }
                // 3. Kirim file sebagai response
                return Storage::disk('public')->download($filePath, $fileName);
            } else if ($request->type === 'pm') {
                $fileName = $assetBelanja->pm_name_file;
                $filePath = $assetBelanja->pm_path;
                if (!Storage::disk('public')->exists($filePath)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'File tidak ditemukan di server.',
                    ], 404); // 404 Not Found
                }
                // 3. Kirim file sebagai response
                return Storage::disk('public')->download($filePath, $fileName);
            }
            return null;
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

    public function destroySertifikatAndScan(Request $request, Asset $asset)
    {
        try {
            $request->validate([
                'type' => 'required|string', // Maksimal 10MB
            ]);
            if ($request->type === 'sertifikat') {
                // 2. Simpan data ke database dalam transaksi
                DB::beginTransaction();
                // 3. Hapus file lama jika ada
                if ($asset->path && Storage::disk('public')->exists($asset->path)) {
                    Storage::disk('public')->delete($asset->path);
                }
                // 5. Perbarui entri database
                $asset->update([
                    'sertifikat_name_file' => null,
                    'sertifikat_path' => null,
                ]);
                DB::commit();
            } else if ($request->type === 'scan') {
                // 2. Simpan data ke database dalam transaksi
                DB::beginTransaction();
                // 3. Hapus file lama jika ada
                if ($asset->path && Storage::disk('public')->exists($asset->path)) {
                    Storage::disk('public')->delete($asset->path);
                }

                // 5. Perbarui entri database
                $asset->update([
                    'scan_name_file' => null,
                    'scan_path' => null,
                ]);
                DB::commit();
            }
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


    public function updateFileSertifikatAndScan(Request $request, Asset $asset)
    {
        try {
            // 1. Validasi input: hanya file yang diwajibkan
            $request->validate([
                'report_file' => 'required|file|mimes:pdf,xls,xlsx,doc,docx|max:10240', // Maksimal 10MB
                'type' => 'required|string', // Maksimal 10MB
            ]);
            if ($request->type == "sertifikat") {
                // 2. Simpan data ke database dalam transaksi
                DB::beginTransaction();

                // 3. Hapus file lama jika ada
                if ($asset->path && Storage::disk('public')->exists($asset->path)) {
                    Storage::disk('public')->delete($asset->path);
                }

                // 4. Unggah file baru
                $file = $request->file('report_file');
                $originalName = $file->getClientOriginalName();
                $path = $file->store('asset', 'public');
                $asset->update([
                    'sertifikat_name_file' => $originalName,
                    'sertifikat_path' => $path,
                ]);
                DB::commit();
            } else if ($request->type == "scan") {
                // 2. Simpan data ke database dalam transaksi
                DB::beginTransaction();

                // 3. Hapus file lama jika ada
                if ($asset->path && Storage::disk('public')->exists($asset->path)) {
                    Storage::disk('public')->delete($asset->path);
                }

                // 4. Unggah file baru
                $file = $request->file('report_file');
                $originalName = $file->getClientOriginalName();
                $path = $file->store('asset', 'public');
                $asset->update([
                    'scan_name_file' => $originalName,
                    'scan_path' => $path,
                ]);
                DB::commit();
            }
            return response()->json([
                'status' => 'success',
                'message' => 'File laporan berhasil diperbarui.',
                'data' => $asset,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui file: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function downloadSertifikatAndScan(Request $request, Asset $asset)
    {
        try {
            $request->validate([
                'type' => 'required|string', // Maksimal 10MB
            ]);
            // 1. Cari data laporan berdasarkan ID
            if ($request->type === 'sertifikat') {
                $fileName = $asset->sertifikat_name_file;
                $filePath = $asset->sertifikat_path;

                if (!Storage::disk('public')->exists($filePath)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'File tidak ditemukan di server.',
                    ], 404); // 404 Not Found
                }
                // 3. Kirim file sebagai response
                return Storage::disk('public')->download($filePath, $fileName);
            } else if ($request->type === 'scan') {
                $fileName = $asset->scan_name_file;
                $filePath = $asset->scan_path;
                if (!Storage::disk('public')->exists($filePath)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'File tidak ditemukan di server.',
                    ], 404); // 404 Not Found
                }
                // 3. Kirim file sebagai response
                return Storage::disk('public')->download($filePath, $fileName);
            }
            return null;
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
