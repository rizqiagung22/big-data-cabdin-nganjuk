<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetBelanja extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'belanja_modal';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'tahun_id',
        'bos_name_file',
        'bos_path',
        'bpopp_name_file',
        'bpopp_path',
        'bp_name_file',
        'bp_path',
        'pm_name_file',
        'pm_path',
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

}
