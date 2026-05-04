<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PesertaMagang;
use App\Models\TimKerja;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $kpi = [
            'menunggu_review' => PesertaMagang::where('status_magang', 'Menunggu Review')->count(),
            'belum_aktif' => PesertaMagang::where('status_magang', 'Belum Aktif')->count(),
            'aktif' => PesertaMagang::where('status_magang', 'Aktif')->count(),
            'selesai' => PesertaMagang::where('status_magang', 'Selesai')->count(),
            'anulir' => PesertaMagang::where('status_magang', 'Anulir')->count(),
            'ditolak' => PesertaMagang::where('status_magang', 'Ditolak')->count(),
        ];

        $timKerja = TimKerja::withAktifCount()->get();

        return view('admin.dashboard.index', compact('kpi', 'timKerja'));
    }
}
