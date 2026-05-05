<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PesertaMagang;

class FotoAksesController extends Controller
{
    /**
     * Menampilkan galeri foto pas foto peserta untuk keperluan akses scan pintu.
     * Menampilkan peserta yang berstatus Menunggu Review + Belum Aktif + Aktif.
     */
    public function index()
    {
        $peserta = PesertaMagang::with(['timKerja1'])
            ->whereIn('status_magang', ['Menunggu Review', 'Belum Aktif', 'Aktif'])
            ->orderByRaw("FIELD(status_magang, 'Aktif', 'Belum Aktif', 'Menunggu Review')")
            ->orderBy('nama')
            ->get();

        $grouped = [
            'aktif'        => $peserta->where('status_magang', 'Aktif'),
            'belum_aktif'  => $peserta->where('status_magang', 'Belum Aktif'),
            'review'       => $peserta->where('status_magang', 'Menunggu Review'),
        ];

        return view('admin.foto-akses.index', compact('grouped'));
    }
}
