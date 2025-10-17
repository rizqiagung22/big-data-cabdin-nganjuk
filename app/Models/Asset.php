<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'asset';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'tahun_id',
        'bm_id_1',
        'bm_id_2',
        'bm_id_3',
        'bm_id_4',
        'scan_name_file',
        'scan_path',
        'sertifikat_name_file',
        'sertifikat_path',
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
    public function belanjaModal1(): BelongsTo
    {
        return $this->belongsTo(AssetBelanja::class, 'bm_id_1');
    }
    public function belanjaModal2(): BelongsTo
    {
        return $this->belongsTo(AssetBelanja::class, 'bm_id_2');
    }

    public function belanjaModal3(): BelongsTo
    {
        return $this->belongsTo(AssetBelanja::class, 'bm_id_3');
    }

    public function belanjaModal4(): BelongsTo
    {
        return $this->belongsTo(AssetBelanja::class, 'bm_id_4');
    }


}
