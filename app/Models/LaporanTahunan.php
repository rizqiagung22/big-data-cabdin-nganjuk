<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanTahunan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'laporan_tahunan';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'tahun_id',
        'lembaga_id',
        'jenis_laporan',
        'path',
    ];


    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array
     */
    protected $casts = [
        'jenis_laporan' => \App\JenisLaporan::class, // Namespace diubah sesuai lokasi file
    ];

    /**
     * Mendefinisikan relasi ke model Tahun.
     *
     * @return BelongsTo
     */
    public function tahun(): BelongsTo
    {
        return $this->belongsTo(Tahun::class);
    }

    /**
     * Mendefinisikan relasi ke model Lembaga.
     *
     * @return BelongsTo
     */
    public function lembaga(): BelongsTo
    {
        return $this->belongsTo(Lembaga::class);
    }
}
