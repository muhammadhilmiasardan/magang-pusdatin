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
            'ditolak' => $peserta->where('status_magang', 'Ditolak'),
        ];

        return view('admin.manajemen.index', compact('grouped'));
    }

    public function show($id)
    {
        $peserta = PesertaMagang::with(['timKerja1', 'timKerja2'])->findOrFail($id);
        return response()->json($peserta);
    }

    /**
     * Mengundurkan diri — ubah status peserta menjadi Anulir.
     * Hanya bisa dilakukan jika status saat ini Belum Aktif atau Aktif.
     */
    public function anulir(Request $request, $id)
    {
        $peserta = PesertaMagang::findOrFail($id);

        if (!in_array($peserta->status_magang, ['Belum Aktif', 'Aktif'])) {
            return response()->json(['error' => 'Hanya peserta Belum Aktif atau Aktif yang bisa mengundurkan diri.'], 422);
        }

        $peserta->status_magang = 'Anulir';
        $peserta->save();

        return response()->json(['success' => true, 'message' => "{$peserta->nama} telah mengundurkan diri (Anulir)."]);
    }
}
