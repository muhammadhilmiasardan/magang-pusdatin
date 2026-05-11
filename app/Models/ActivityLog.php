<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'tipe_aksi', 'deskripsi'];

    /**
     * Relasi ke admin yang melakukan aksi.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper static function untuk mempermudah mencatat log aktivitas.
     * 
     * @param string $tipe_aksi (e.g. 'terima_lamaran', 'tolak_lamaran', 'kirim_dokumen', 'manajemen_admin')
     * @param string $deskripsi
     * @return void
     */
    public static function log($tipe_aksi, $deskripsi)
    {
        if (Auth::check()) {
            self::create([
                'user_id' => Auth::id(),
                'tipe_aksi' => $tipe_aksi,
                'deskripsi' => $deskripsi,
            ]);
        }
    }
}
