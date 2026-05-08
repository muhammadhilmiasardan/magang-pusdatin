<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PesertaMagang;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class AktifkanPesertaMagang
{
    /**
     * Middleware ini berjalan pada setiap request ke halaman admin.
     * Secara otomatis mengubah status peserta dari "Belum Aktif" ke "Aktif"
     * apabila tanggal mulai magang mereka sudah tiba — tanpa perlu cron job.
     *
     * Efek: setiap kali admin membuka halaman admin manapun, sistem cek
     * dan langsung update DB jika ada yang perlu diaktifkan.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya jalankan untuk route admin (bukan untuk form publik pendaftaran)
        if ($request->is('admin*')) {
            PesertaMagang::where('status_magang', 'Belum Aktif')
                ->whereDate('tanggal_mulai', '<=', Carbon::today())
                ->update(['status_magang' => 'Aktif']);
        }

        return $next($request);
    }
}
