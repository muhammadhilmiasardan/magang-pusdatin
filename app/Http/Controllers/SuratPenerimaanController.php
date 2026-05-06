<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PesertaMagang;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SuratPenerimaanController extends Controller
{
    /**
     * Preview surat penerimaan (HTML, untuk ditampilkan di modal/iframe).
     */
    public function preview(Request $request, $id)
    {
        $peserta = PesertaMagang::with(['timKerja1', 'timKerja2'])->findOrFail($id);

        $data = $this->buildSuratData($request, $peserta);
        $data['is_pdf'] = false;

        $html = view('admin.surat.template', $data)->render();

        return response($html)->header('Content-Type', 'text/html');
    }

    /**
     * Generate & download PDF surat penerimaan.
     */
    public function download(Request $request, $id)
    {
        $request->validate([
            'nomor_surat'       => 'required|string',
            'yth'               => 'required|string',
            'nomor_surat_univ'  => 'required|string',
            'tanggal_surat_lamaran' => 'required|string',
            'id_tim_kerja_ditempatkan' => 'required|exists:tim_kerja,id',
        ]);

        $peserta = PesertaMagang::with(['timKerja1', 'timKerja2'])->findOrFail($id);

        $data = $this->buildSuratData($request, $peserta);
        $data['is_pdf'] = true;

        $pdf = Pdf::loadView('admin.surat.template', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'DejaVu Sans',
            ]);

        $filename = 'Surat_Penerimaan_' . str_replace(' ', '_', $peserta->nama) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Terima lamaran + set penempatan + (opsional langsung download surat).
     */
    public function terima(Request $request, $id)
    {
        $request->validate([
            'id_tim_kerja_ditempatkan' => 'required|exists:tim_kerja,id',
        ]);

        $peserta = PesertaMagang::findOrFail($id);

        // Ubah status
        $peserta->status_magang       = 'Belum Aktif';
        // Tetapkan penempatan final = pilihan yang dipilih admin
        // Simpan di id_tim_kerja_1 sebagai "penempatan resmi"
        $peserta->id_tim_kerja_1      = $request->id_tim_kerja_ditempatkan;
        $peserta->save();

        return redirect()->route('admin.lamaran.index')
            ->with('success', "Lamaran {$peserta->nama} diterima. Penempatan: Tim Kerja ID {$request->id_tim_kerja_ditempatkan}.");
    }

    // ─────────────────────────────────────────────────────
    private function buildSuratData(Request $request, PesertaMagang $peserta): array
    {
        $timDitempatkan = \App\Models\TimKerja::find($request->id_tim_kerja_ditempatkan);

        $tingkat = $peserta->tingkat_pendidikan;
        $isMahasiswa = strtolower($tingkat) === 'universitas';
        $sebutanPeserta = $isMahasiswa ? 'Mahasiswa/i' : 'Siswa/i';
        $sebutanSatuan  = $isMahasiswa ? 'Mahasiswa'   : 'Siswa';

        $tanggalMulai   = Carbon::parse($peserta->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($peserta->tanggal_selesai);

        // Convert logo to base64 for reliable display in both browser and DOMPDF
        $logoPath = base_path('logo_pu.png');
        $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
        $logoBase64 = 'data:image/png;base64,' . $logoData;

        return [
            'peserta'           => $peserta,
            'nomor_surat'       => $request->nomor_surat ?? '',
            'yth'               => $request->yth ?? '',
            'nomor_surat_univ'  => $request->nomor_surat_univ ?? '',
            'tanggal_surat_lamaran' => $request->tanggal_surat_lamaran ?? '',
            'tanggal_terbit'    => Carbon::now()->translatedFormat('d F Y'),
            'tim_ditempatkan'   => $timDitempatkan,
            'sebutan_peserta'   => $sebutanPeserta,
            'sebutan_satuan'    => $sebutanSatuan,
            'tanggal_mulai_fmt' => $tanggalMulai->translatedFormat('d F Y'),
            'tanggal_selesai_fmt' => $tanggalSelesai->translatedFormat('d F Y'),
            'logo_base64'       => $logoBase64,
        ];
    }
}
