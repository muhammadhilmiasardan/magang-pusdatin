<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaMagang extends Model
{
    protected $table = 'peserta_magang';

    protected $fillable = [
        'nama',
        'tingkat_pendidikan',
        'nama_institusi',
        'jurusan',
        'nim_nis',
        'tanggal_mulai',
        'tanggal_selesai',
        'nomor_telp',
        'email',
        'email_institusi',
        'id_tim_kerja_1',
        'id_tim_kerja_2',
        'cv',
        'surat_rekomendasi',
        'surat_penerimaan_final',
        'pas_foto',
        'status_magang',
        'is_sk_sent',
        'is_evaluasi_sent',
        'is_sertifikat_sent',
        'surat_keterangan',
        'evaluasi_data',
        'surat_evaluasi',
        'sertifikat_data',
        'surat_sertifikat',
    ];

    /**
     * Relasi ke tim kerja pilihan 1.
     */
    public function timKerja1()
    {
        return $this->belongsTo(TimKerja::class, 'id_tim_kerja_1');
    }

    /**
     * Relasi ke tim kerja pilihan 2.
     */
    public function timKerja2()
    {
        return $this->belongsTo(TimKerja::class, 'id_tim_kerja_2');
    }
}
