<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\PesertaMagang;
use App\Mail\SuratPenerimaanMail;

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

    /**
     * Step 3: Admin upload surat final yang sudah di-TTE.
     * Menyimpan file dan memperbarui id_tim_kerja_1 (penempatan resmi).
     */
    public function uploadSurat(Request $request, $id)
    {
        $request->validate([
            'surat_final'              => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'id_tim_kerja_ditempatkan' => 'required|exists:tim_kerja,id',
        ], [
            'surat_final.required' => 'File surat penerimaan wajib diupload.',
            'surat_final.mimes'    => 'Format file harus PDF, JPG, atau PNG.',
            'surat_final.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        $peserta = PesertaMagang::findOrFail($id);

        // Hapus file lama jika ada
        if ($peserta->surat_penerimaan_final) {
            $oldPath = storage_path('app/public/' . $peserta->surat_penerimaan_final);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        // Simpan file surat final ke storage
        $path = $request->file('surat_final')
                    ->store('dokumen/surat-penerimaan-final', 'public');

        // Update penempatan resmi + path surat
        $peserta->update([
            'id_tim_kerja_1'          => $request->id_tim_kerja_ditempatkan,
            'surat_penerimaan_final'   => $path,
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Surat berhasil diupload.',
            'file_path' => $path,
            'file_url'  => asset('storage/' . $path),
        ]);
    }

    /**
     * Step 4: Kirim email ke pelamar dengan surat terlampir + ubah status ke Belum Aktif.
     */
    public function kirimEmail(Request $request, $id)
    {
        $request->validate([
            'caption' => 'required|string',
        ]);

        $peserta = PesertaMagang::with(['timKerja1', 'timKerja2'])->findOrFail($id);

        if (!$peserta->surat_penerimaan_final) {
            return response()->json([
                'success' => false,
                'message' => 'Surat penerimaan belum diupload. Harap upload terlebih dahulu.',
            ], 422);
        }

        // Kirim email
        Mail::to($peserta->email)
            ->send(new SuratPenerimaanMail($peserta, $request->caption));

        // Ubah status menjadi Belum Aktif
        $peserta->status_magang = 'Belum Aktif';
        $peserta->save();

        return response()->json([
            'success' => true,
            'message' => "Email berhasil dikirim ke {$peserta->email}. Status {$peserta->nama} sekarang: Belum Aktif.",
        ]);
    }

    public function tolak(Request $request, $id)
    {
        $peserta = PesertaMagang::findOrFail($id);

        $peserta->status_magang = 'Ditolak';
        $peserta->save();

        return redirect()->route('admin.lamaran.index')
            ->with('success', "Lamaran {$peserta->nama} telah ditolak.");
    }
}
