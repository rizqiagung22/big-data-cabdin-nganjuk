<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // Data contoh untuk tabel laporan_tahunan.
        // Asumsi: nilai 'tahun_id' dan 'lembaga_id' yang direferensikan sudah ada.
        $data = [
            [
                'tahun_id' => 1,
                'lembaga_id' => 1,
                'jenis_laporan' => 'usulan',
                'path' => 'laporan/usulan/lembaga_1_tahun_1.pdf',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'tahun_id' => 1,
                'lembaga_id' => 1,
                'jenis_laporan' => 'pagu',
                'path' => 'laporan/pagu/lembaga_1_tahun_1.pdf',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'tahun_id' => 1,
                'lembaga_id' => 2,
                'jenis_laporan' => 'usulan',
                'path' => 'laporan/usulan/lembaga_2_tahun_1.pdf',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'tahun_id' => 2,
                'lembaga_id' => 1,
                'jenis_laporan' => 'usulan',
                'path' => 'laporan/usulan/lembaga_1_tahun_2.pdf',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'tahun_id' => 2,
                'lembaga_id' => 2,
                'jenis_laporan' => 'realisasi',
                'path' => 'laporan/realisasi/lembaga_2_tahun_2.pdf',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Masukkan data ke dalam tabel 'table_laporan_tahunan'
        DB::table('table_laporan_tahunan')->insert($data);
    }
}
