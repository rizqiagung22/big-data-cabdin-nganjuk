<?php

namespace App;

enum JenisLaporan: string
{
    case PAGU = 'pagu';
    case RKAS = 'rkas';
    case USULAN_PER_BULAN = 'usulan per bulan';
    case REALISASI = 'realisasi';
    case PENYERAPAN_TIAP_BULAN = 'penyerapan tiap bulan';
}
