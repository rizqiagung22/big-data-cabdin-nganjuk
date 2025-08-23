<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tahun extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'tahun';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'tahun',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    /**
     * Relasi dengan tabel laporan_tahunan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function laporanTahunan()
    {
        return $this->hasMany(LaporanTahunan::class, 'tahun_id');
    }
}
