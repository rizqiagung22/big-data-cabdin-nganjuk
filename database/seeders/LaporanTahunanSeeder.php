<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanTahunanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Mendefinisikan semua jenis laporan yang tersedia
        $jenisLaporan = ['pagu', 'rkas', 'usulan per bulan', 'realisasi', 'penyerapan tiap bulan'];
        $tahunIds = [1, 2];
        $lembagaIds = range(1, 97);
        $data = []; // Array untuk menyimpan semua data

        // Buat kombinasi unik untuk setiap lembaga, tahun, dan jenis laporan
        foreach ($lembagaIds as $lembagaId) {
            foreach ($tahunIds as $tahunId) {
                foreach ($jenisLaporan as $jenis) {
                    $data[] = [
                        'tahun_id' => $tahunId,
                        'lembaga_id' => $lembagaId,
                        'jenis_laporan' => $jenis,
                        'path' => 'laporan/' . str_replace(' ', '_', $jenis) . '/lembaga_' . $lembagaId . '_tahun_' . $tahunId . '.pdf',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }
        }

        // Masukkan semua data ke dalam tabel 'laporan_tahunan'
        DB::table('laporan_tahunan')->insert($data);
    }
}
