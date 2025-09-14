<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LembagaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data dari file Excel
        $data = [
//            ['nama_satuan_pendidikan' => 'SLB AL - KHARIQ', 'npsn' => '69978385', 'bentuk_pendidikan' => 'SLB', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SLB AL IKHSAN', 'npsn' => '70007597', 'bentuk_pendidikan' => 'SLB', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SLB AL IKHSAN 1 BAGOR', 'npsn' => '69831497', 'bentuk_pendidikan' => 'SLB', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SLB AL IKHSAN 2 BARON', 'npsn' => '69904837', 'bentuk_pendidikan' => 'SLB', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SLB Dharma Bakti Patianrowo', 'npsn' => '20549449', 'bentuk_pendidikan' => 'SLB', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SLB Krida Utama 1 Tanjunganom', 'npsn' => '20539809', 'bentuk_pendidikan' => 'SLB', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SLB KRIDA UTAMA 2 LOCERET', 'npsn' => '69727411', 'bentuk_pendidikan' => 'SLB', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SLB MUHAMMADIYAH KERTOSONO', 'npsn' => '20539810', 'bentuk_pendidikan' => 'SLB', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SLB NEGERI SAMBIREJO NGANJUK', 'npsn' => '20514050', 'bentuk_pendidikan' => 'SLB', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SLB SHANTI KOSALA MASTRIP', 'npsn' => '20538329', 'bentuk_pendidikan' => 'SLB', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SLB TUNAS MULIA REJOSO', 'npsn' => '69727418', 'bentuk_pendidikan' => 'SLB', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMA BINA INSAN MANDIRI', 'npsn' => '69880982', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMA ISLAM AL-QODIR', 'npsn' => '69881315', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMA ISLAM ASSYAFIAH', 'npsn' => '69965840', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMA ISLAM INSAN CENDEKIA BAITUL IZZAH', 'npsn' => '69830668', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMA MUHAMMADIYAH 2 KERTOSONO', 'npsn' => '20513137', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMA MUHAMMADIYAH 3 TANJUNGANOM', 'npsn' => '20538320', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMA NEGERI 1 BERBEK NGANJUK', 'npsn' => '20538330', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMA NEGERI 1 GONDANG NGANJUK', 'npsn' => '20538393', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMA NEGERI 1 KERTOSONO NGANJUK', 'npsn' => '20538394', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMA NEGERI 1 LOCERET NGANJUK', 'npsn' => '20538322', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMA NEGERI 1 NGANJUK', 'npsn' => '20538321', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMA NEGERI 1 NGRONGGOT NGANJUK', 'npsn' => '20539811', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMA NEGERI 1 PACE NGANJUK', 'npsn' => '20538323', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMA NEGERI 1 PATIANROWO NGANJUK', 'npsn' => '20538324', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMA NEGERI 1 PRAMBON NGANJUK', 'npsn' => '20538395', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMA NEGERI 1 REJOSO NGANJUK', 'npsn' => '20538396', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMA NEGERI 1 SUKOMORO NGANJUK', 'npsn' => '20538328', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMA NEGERI 1 TANJUNGANOM NGANJUK', 'npsn' => '20538325', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMA NEGERI 2 NGANJUK', 'npsn' => '20538326', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMA NEGERI 3 NGANJUK', 'npsn' => '20538327', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMA PGRI LENGKONG', 'npsn' => '20538391', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMA PGRI PACE', 'npsn' => '20538392', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMA POMOSDA TANJUNGANOM', 'npsn' => '20536949', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMAS DIPONEGORO NGANJUK', 'npsn' => '20513142', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMAS MUHAMMADIYAH 1', 'npsn' => '20538319', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMAS PGRI GONDANG', 'npsn' => '20538390', 'bentuk_pendidikan' => 'SMA', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK AINUL YAQIN BAGOR', 'npsn' => '69918943', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK AL BASTHOMI LOCERET', 'npsn' => '20539813', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan pendidikan' => 'SMK AL FATTAH KERTOSONO NGANJUK', 'npsn' => '20536951', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK AL HUSNA', 'npsn' => '20538332', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK AL KAUTSAR', 'npsn' => '69727400', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK AL KHIDMAH NGRONGGOT', 'npsn' => '20570617', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK BAQU', 'npsn' => '70023534', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK BHAKTI NORMA HUSADA', 'npsn' => '69954457', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK BUDI UTOMO KERTOSONO', 'npsn' => '69989236', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK DARUL HIKAM', 'npsn' => '20577510', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK DAWATUL KHOIR KERTOSONO', 'npsn' => '69968272', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK Dr. WAHIDIN', 'npsn' => '20577378', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK HAMALATUL `ILMI', 'npsn' => '69988485', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK HAQ', 'npsn' => '70035390', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK INTENSIF BAITUSSALAM TANJUNGANOM', 'npsn' => '69727423', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK ISLAM AL QOMAR', 'npsn' => '69888527', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK ISLAM AL-FATTAH TANJUNGANOM', 'npsn' => '70003593', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK ISLAM MIFTAHUL ULUM', 'npsn' => '69965592', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK ISLAM ULUL ALBAB NGRONGGOT', 'npsn' => '20539815', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK ISYHAR GROMPOL PRAMBON', 'npsn' => '69907557', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK KUSUMA NEGARA', 'npsn' => '69904827', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK MUHAMMADIYAH 1 BARON', 'npsn' => '20513156', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK MUHAMMADIYAH 1 BERBEK', 'npsn' => '20538335', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK MUHAMMADIYAH 1 KERTOSONO', 'npsn' => '20538336', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK MUHAMMADIYAH 1 NGANJUK', 'npsn' => '20538337', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK MUHAMMADIYAH 1 PRAMBON', 'npsn' => '20536953', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK MUHAMMADIYAH 2 NGANJUK', 'npsn' => '20538338', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK MUHAMMADIYAH 3 NGANJUK', 'npsn' => '20538339', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK Nasional Nganjuk', 'npsn' => '20513152', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK NEGERI 1 BAGOR NGANJUK', 'npsn' => '20538340', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMK NEGERI 1 GONDANG NGANJUK', 'npsn' => '20513253', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMK NEGERI 1 KERTOSONO NGANJUK', 'npsn' => '20538341', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMK NEGERI 1 LENGKONG NGANJUK', 'npsn' => '20554405', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMK NEGERI 1 NGANJUK', 'npsn' => '20538342', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMK NEGERI 1 TANJUNGANOM NGANJUK', 'npsn' => '20571770', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMK NEGERI 2 BAGOR NGANJUK', 'npsn' => '69727424', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMK NEGERI 2 NGANJUK', 'npsn' => '20538343', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Negeri'],
//            ['nama_satuan_pendidikan' => 'SMK NU PACE', 'npsn' => '20555647', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK NU WILANGAN', 'npsn' => '20577488', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK PEMBANGUNAN REJOSO', 'npsn' => '20536954', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK PGRI 1 NGANJUK', 'npsn' => '20538346', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK PGRI 1 TANJUNGANOM', 'npsn' => '69894475', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK PGRI 2 KERTOSONO', 'npsn' => '20538347', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK PGRI 2 NGANJUK', 'npsn' => '20538348', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK PGRI 3 NGANJUK', 'npsn' => '20538349', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK PLUS RADEN SYAHID TANJUNGANOM', 'npsn' => '69979533', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK PSM WARUJAYENG', 'npsn' => '20538351', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK ROUDLOTUL MUSLIMIN', 'npsn' => '20554588', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK SATRIA BHAKTI NGANJUK', 'npsn' => '69775814', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMK TARUNA BAKTI KERTOSONO', 'npsn' => '20538350', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMKS AL AMIN NGETOS', 'npsn' => '69727391', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMKS AL ASYARIYAH PRAMBON', 'npsn' => '20539812', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMKS AL KHOIRIYAH', 'npsn' => '20539814', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMKS BAITUL ATIEQ', 'npsn' => '20557253', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMKS DR SOETOMO NGANJUK', 'npsn' => '20538331', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMKS FARMASI KATOLIK WIYATA FARMA KERTOSONO', 'npsn' => '20570615', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMKS KESEHATAN DHERMAJAYA TANJUNGANOM', 'npsn' => '69775813', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMKS KOSGORO NGANJUK', 'npsn' => '20538334', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMKS PGRI 1 KERTOSONO', 'npsn' => '20538344', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
//            ['nama_satuan_pendidikan' => 'SMKS PGRI NGETOS', 'npsn' => '69759303', 'bentuk_pendidikan' => 'SMK', 'status_sekolah' => 'Swasta'],
            //
            ['nama_satuan_pendidikan' => 'Negeri', 'npsn' => null, 'bentuk_pendidikan' => null, 'status_sekolah' => null],
            ['nama_satuan_pendidikan' => 'Swasta', 'npsn' => null, 'bentuk_pendidikan' => null, 'status_sekolah' => null],
        ];

        // Masukkan data ke dalam tabel 'sekolah'
        DB::table('lembaga')->insert($data);
    }
}
