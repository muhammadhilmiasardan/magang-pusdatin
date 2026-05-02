<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PesertaMagang;

class ManajemenMagangController extends Controller
{
    public function index()
    {
        $peserta = PesertaMagang::with(['timKerja1'])->orderBy('created_at', 'desc')->get();
        
        $grouped = [
            'belum_aktif' => $peserta->where('status_magang', 'Belum Aktif'),
            'aktif' => $peserta->where('status_magang', 'Aktif'),
            'selesai' => $peserta->where('status_magang', 'Selesai'),
            'anulir' => $peserta->where('status_magang', 'Anulir'),
        ];

        return view('admin.manajemen.index', compact('grouped'));
    }

    public function show($id)
    {
        $peserta = PesertaMagang::with(['timKerja1', 'timKerja2'])->findOrFail($id);
        return response()->json($peserta);
    }
}
