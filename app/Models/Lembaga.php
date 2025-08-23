<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Lembaga extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'lembaga';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'nama_satuan_pendidikan',
        'npsn',
        'bentuk_pendidikan',
        'status_sekolah',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array
     */
    /**
     * Relasi dengan tabel laporan_tahunan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function laporanTahunan()
    {
        return $this->hasMany(LaporanTahunan::class, 'lembaga_id');
    }
}
