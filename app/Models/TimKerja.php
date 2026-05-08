<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TimKerja extends Model
{
    protected $table = 'tim_kerja';

    protected $fillable = [
        'nama_tim',
        'bidang',
        'kuota_maksimal',
    ];

    /**
     * Peserta magang yang memilih tim ini sebagai pilihan 1.
     */
    public function pesertaMagangPilihan1()
    {
        return $this->hasMany(PesertaMagang::class, 'id_tim_kerja_1');
    }

    /**
     * Peserta magang yang memilih tim ini sebagai pilihan 2.
     */
    public function pesertaMagangPilihan2()
    {
        return $this->hasMany(PesertaMagang::class, 'id_tim_kerja_2');
    }

    /**
     * Scope untuk menghitung total peserta aktif yang ditempatkan di tim ini
     * (baik dari pilihan 1 maupun pilihan 2).
     * Digunakan oleh controller melalui withCount(['pesertaMagang']).
     */
    public function pesertaMagang()
    {
        return $this->hasMany(PesertaMagang::class, 'id_tim_kerja_1');
    }

    /**
     * Hitung peserta yang SUDAH DITERIMA (mengurangi kuota).
     * Mencakup 'Belum Aktif' (diterima, belum mulai) dan 'Aktif' (sedang magang).
     * 'Ditolak', 'Anulir', 'Selesai', 'Menunggu Review' tidak mengurangi kuota.
     */
    public function scopeWithAktifCount(Builder $query)
    {
        return $query
            ->withCount([
                'pesertaMagangPilihan1 as peserta_magang_count' => function ($q) {
                    $q->whereIn('status_magang', ['Belum Aktif', 'Aktif']);
                },
            ]);
    }
}
