<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TahunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Data dengan format string
        $data = [
            [
                'tahun' => '2023',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'tahun' => '2024',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'tahun' => '2026',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Masukkan data ke dalam tabel 'tahun'
        DB::table('tahun')->insert($data);
    }
}
