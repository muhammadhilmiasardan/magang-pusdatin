<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PesertaMagang;

class LamaranMasukController extends Controller
{
    public function index()
    {
        $lamaran = PesertaMagang::with(['timKerja1', 'timKerja2'])
                    ->where('status_magang', 'Menunggu Review')
                    ->orderBy('created_at', 'desc')
                    ->get();
        return view('admin.lamaran.index', compact('lamaran'));
    }

    public function show($id)
    {
        $peserta = PesertaMagang::with(['timKerja1', 'timKerja2'])->findOrFail($id);
        return response()->json($peserta);
    }

    public function terima(Request $request, $id)
    {
        $peserta = PesertaMagang::findOrFail($id);
        
        // Simulasikan trigger generate Surat Penerimaan dan kirim email
        // Logic email bisa ditambahkan di sini nantinya

        $peserta->status_magang = 'Belum Aktif';
        $peserta->save();

        return redirect()->route('admin.lamaran.index')->with('success', "Lamaran {$peserta->nama} berhasil diterima. Status menjadi Belum Aktif.");
    }

    public function tolak(Request $request, $id)
    {
        $peserta = PesertaMagang::findOrFail($id);
        
        $peserta->status_magang = 'Ditolak';
        $peserta->save();

        return redirect()->route('admin.lamaran.index')->with('success', "Lamaran {$peserta->nama} telah ditolak.");
    }
}
