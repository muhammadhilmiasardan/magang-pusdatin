<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PesertaMagang;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class PusatDokumenController extends Controller
{
    public function index()
    {
        // 1. SK Magang (Peserta Aktif yang belum dikirim SK)
        $skMagang = PesertaMagang::where('status_magang', 'Aktif')
            ->whereDate('tanggal_selesai', '<=', Carbon::today())
            ->where('is_sk_sent', 0)
            ->get();

        // 2. Evaluasi
        // Peserta Aktif yang tanggal selesainya <= hari ini dan belum dikirim evaluasinya
        $evaluasi = PesertaMagang::where('status_magang', 'Aktif')
            ->whereDate('tanggal_selesai', '<=', Carbon::today())
            ->where('is_evaluasi_sent', 0)
            ->get();

        // 3. Sertifikat Kelulusan
        // Peserta Aktif yang tanggal selesainya <= hari ini dan belum dikirim sertifikatnya
        $sertifikat = PesertaMagang::where('status_magang', 'Aktif')
            ->whereDate('tanggal_selesai', '<=', Carbon::today())
            ->where('is_sertifikat_sent', 0)
            ->get();

        return view('admin.dokumen.index', compact('skMagang', 'evaluasi', 'sertifikat'));
    }

    private function buildSkData($peserta, Request $request)
    {
        $logoPath = base_path('logo_pu.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $sebutan_peserta = (strtolower($peserta->tingkat_pendidikan) == 'smk' || strtolower($peserta->tingkat_pendidikan) == 'slta') ? 'Siswa/i' : 'Mahasiswa/i';
        
        $tim_kerja = '';
        if ($peserta->timKerja1) {
            $tim_kerja = $peserta->timKerja1->nama_tim . ', ' . $peserta->timKerja1->bidang;
        }

        Carbon::setLocale('id');

        return [
            'peserta' => $peserta,
            'sebutan_peserta' => $sebutan_peserta,
            'tim_kerja' => $tim_kerja,
            'nomor_surat' => $request->query('nomor_surat', ''),
            'tanggal_mulai' => Carbon::parse($peserta->tanggal_mulai)->translatedFormat('d F Y'),
            'tanggal_selesai' => Carbon::parse($peserta->tanggal_selesai)->translatedFormat('d F Y'),
            'tanggal_terbit' => Carbon::parse($peserta->tanggal_selesai)->translatedFormat('d F Y'), // Hari H selesai
            'logo_base64' => $logoBase64,
        ];
    }

    public function previewSkMagang(Request $request, $id)
    {
        $peserta = PesertaMagang::with(['timKerja1'])->findOrFail($id);
        
        $data = $this->buildSkData($peserta, $request);
        $data['is_pdf'] = false;

        $html = view('admin.dokumen.template-sk-magang', $data)->render();
        return response($html)->header('Content-Type', 'text/html');
    }

    public function downloadSkMagang(Request $request, $id)
    {
        $peserta = PesertaMagang::with(['timKerja1'])->findOrFail($id);
        
        $data = $this->buildSkData($peserta, $request);
        $data['is_pdf'] = true;

        $pdf = Pdf::loadView('admin.dokumen.template-sk-magang', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
            ]);

        $filename = 'SK_Magang_' . str_replace(' ', '_', $peserta->nama) . '.pdf';
        return $pdf->download($filename);
    }

    public function uploadDanKirimSkMagang(Request $request, $id)
    {
        $request->validate([
            'surat_ttd' => 'required|mimes:pdf|max:5120', // max 5MB
            'pesan_email' => 'nullable|string',
        ]);

        $peserta = PesertaMagang::findOrFail($id);

        // Upload File
        if ($request->hasFile('surat_ttd')) {
            $file = $request->file('surat_ttd');
            $filename = 'SK_Magang_TTD_' . time() . '_' . str_replace(' ', '_', $peserta->nama) . '.pdf';
            $path = $file->storeAs('public/surat_keterangan', $filename);
            
            // Save path to DB
            $peserta->surat_keterangan = 'surat_keterangan/' . $filename;
            $peserta->is_sk_sent = 1;
            // Optionally, change status to Selesai if not already
            // User requested that admin completes SK, Evaluasi, Sertifikat BEFORE changing to Selesai.
            // So we don't change status_magang here.
            $peserta->save();
        }

        // Send Email
        try {
            Mail::send('emails.surat-keterangan', [
                'peserta' => $peserta,
                'pesan_tambahan' => $request->pesan_email
            ], function ($message) use ($peserta) {
                $message->to($peserta->email)
                        ->subject('Surat Keterangan Selesai Magang PUSDATIN PUPR');
                
                if ($peserta->surat_keterangan && Storage::disk('public')->exists($peserta->surat_keterangan)) {
                    $message->attach(Storage::disk('public')->path($peserta->surat_keterangan), [
                        'as' => 'Surat_Keterangan_Magang_' . $peserta->nama . '.pdf',
                        'mime' => 'application/pdf',
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Surat Keterangan Magang berhasil diunggah dan dikirim ke email peserta.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage()
            ], 500);
        }
    }

    // =========================================================================
    // EVALUASI MAGANG METHODS
    // =========================================================================

    public function saveDraftEvaluasi(Request $request, $id)
    {
        $request->validate([
            'nomor_surat' => 'nullable|string',
            'kepada_yth' => 'nullable|string',
            'nilai_1' => 'nullable|numeric|min:0|max:10',
            'nilai_2' => 'nullable|numeric|min:0|max:10',
            'nilai_3' => 'nullable|numeric|min:0|max:10',
            'nilai_4' => 'nullable|numeric|min:0|max:10',
            'nilai_5' => 'nullable|numeric|min:0|max:10',
            'nilai_6' => 'nullable|numeric|min:0|max:10',
            'nilai_7' => 'nullable|numeric|min:0|max:10',
            'nilai_8' => 'nullable|numeric|min:0|max:10',
            'nilai_9' => 'nullable|numeric|min:0|max:10',
            'nilai_10' => 'nullable|numeric|min:0|max:10',
        ]);

        $peserta = PesertaMagang::findOrFail($id);
        $peserta->evaluasi_data = json_encode($request->except('_token'));
        $peserta->save();

        return response()->json(['success' => true, 'message' => 'Draft evaluasi berhasil disimpan.']);
    }

    private function buildEvaluasiData($peserta)
    {
        $logoPath = base_path('logo_pu.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $tim_kerja = '';
        $ketua_tim = '';
        $nip_ketua_tim = '';

        if ($peserta->timKerja1) {
            $tim_kerja = $peserta->timKerja1->nama_tim;
            $ketua_tim = $peserta->timKerja1->ketua_tim ?? '-';
            $nip_ketua_tim = $peserta->timKerja1->nip_ketua_tim ?? '-';
        }

        $evaluasi_data = $peserta->evaluasi_data ? json_decode($peserta->evaluasi_data, true) : [];
        
        $nilai = [
            (float)($evaluasi_data['nilai_1'] ?? 0),
            (float)($evaluasi_data['nilai_2'] ?? 0),
            (float)($evaluasi_data['nilai_3'] ?? 0),
            (float)($evaluasi_data['nilai_4'] ?? 0),
            (float)($evaluasi_data['nilai_5'] ?? 0),
            (float)($evaluasi_data['nilai_6'] ?? 0),
            (float)($evaluasi_data['nilai_7'] ?? 0),
            (float)($evaluasi_data['nilai_8'] ?? 0),
            (float)($evaluasi_data['nilai_9'] ?? 0),
            (float)($evaluasi_data['nilai_10'] ?? 0),
        ];

        $total_nilai = array_sum($nilai);

        Carbon::setLocale('id');

        return [
            'peserta' => $peserta,
            'tim_kerja' => $tim_kerja,
            'ketua_tim' => $ketua_tim,
            'nip_ketua_tim' => $nip_ketua_tim,
            'tanggal_mulai' => Carbon::parse($peserta->tanggal_mulai)->translatedFormat('d F Y'),
            'tanggal_selesai' => Carbon::parse($peserta->tanggal_selesai)->translatedFormat('d F Y'),
            'tanggal_terbit' => Carbon::today()->translatedFormat('d F Y'),
            'logo_base64' => $logoBase64,
            'nomor_surat' => $evaluasi_data['nomor_surat'] ?? '',
            'kepada_yth' => $evaluasi_data['kepada_yth'] ?? "Bapak/Ibu ....\n" . $peserta->nama_institusi,
            'nilai' => $nilai,
            'total_nilai' => $total_nilai,
        ];
    }

    public function previewEvaluasi($id)
    {
        $peserta = PesertaMagang::with(['timKerja1'])->findOrFail($id);
        
        $data = $this->buildEvaluasiData($peserta);
        $data['is_pdf'] = false;

        $html = view('admin.dokumen.template-evaluasi', $data)->render();
        return response($html)->header('Content-Type', 'text/html');
    }

    public function downloadEvaluasi($id)
    {
        $peserta = PesertaMagang::with(['timKerja1'])->findOrFail($id);
        
        $data = $this->buildEvaluasiData($peserta);
        $data['is_pdf'] = true;

        $pdf = Pdf::loadView('admin.dokumen.template-evaluasi', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
            ]);

        $filename = 'Evaluasi_Magang_' . str_replace(' ', '_', $peserta->nama) . '.pdf';
        return $pdf->download($filename);
    }

    public function uploadDanKirimEvaluasi(Request $request, $id)
    {
        $request->validate([
            'surat_ttd' => 'required|mimes:pdf|max:5120', // max 5MB
            'pesan_email' => 'nullable|string',
        ]);

        $peserta = PesertaMagang::findOrFail($id);

        // Upload File
        if ($request->hasFile('surat_ttd')) {
            $file = $request->file('surat_ttd');
            $filename = 'Evaluasi_Magang_TTD_' . time() . '_' . str_replace(' ', '_', $peserta->nama) . '.pdf';
            $path = $file->storeAs('public/surat_evaluasi', $filename);
            
            $peserta->surat_evaluasi = 'surat_evaluasi/' . $filename;
            $peserta->is_evaluasi_sent = 1;
            
            // Check if status should be updated to Selesai
            if ($peserta->is_sk_sent && $peserta->is_evaluasi_sent && $peserta->is_sertifikat_sent) {
                $peserta->status_magang = 'Selesai';
            }
            
            $peserta->save();
        }

        // Send Email
        try {
            Mail::send('emails.surat-evaluasi', [
                'peserta' => $peserta,
                'pesan_tambahan' => $request->pesan_email
            ], function ($message) use ($peserta) {
                $message->to($peserta->email)
                        ->subject('Lembar Evaluasi Magang PUSDATIN PUPR');
                
                if ($peserta->surat_evaluasi && Storage::disk('public')->exists($peserta->surat_evaluasi)) {
                    $message->attach(Storage::disk('public')->path($peserta->surat_evaluasi), [
                        'as' => 'Evaluasi_Magang_' . $peserta->nama . '.pdf',
                        'mime' => 'application/pdf',
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Surat Evaluasi berhasil diunggah dan dikirim ke email peserta.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage()
            ], 500);
        }
    }

    // =========================================================================
    // SERTIFIKAT MAGANG METHODS
    // =========================================================================

    public function saveDraftSertifikat(Request $request, $id)
    {
        $request->validate([
            'nomor_sertifikat' => 'nullable|string',
            'predikat'         => 'nullable|string',
        ]);

        $peserta = PesertaMagang::findOrFail($id);
        $peserta->sertifikat_data = json_encode($request->only('nomor_sertifikat', 'predikat'));
        $peserta->save();

        return response()->json(['success' => true, 'message' => 'Draft sertifikat berhasil disimpan.']);
    }

    private function buildSertifikatData($peserta)
    {
        // Encode semua aset gambar ke base64 agar dompdf & preview browser sama-sama bisa render
        $assetPaths = [
            'logo_pu_base64'       => public_path('logo_pu.png'),
            'logo_pusdatin_base64' => public_path('logo_PUSDATIN.png'),
            'logo_bwk_base64'      => public_path('Wilayah_Bebas_dari_Korupsi.png'),
            'bg_bingkai_base64'    => public_path('bg_bingkai.png'),
        ];

        $assets = [];
        foreach ($assetPaths as $key => $path) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $mime = $ext === 'jpg' || $ext === 'jpeg' ? 'image/jpeg' : 'image/png';
            $assets[$key] = file_exists($path)
                ? 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path))
                : '';
        }

        // Font Monotype Corsiva untuk DomPDF
        $fontPath = public_path('fonts/MTCORSVA.TTF');
        $assets['font_corsiva_base64'] = file_exists($fontPath)
            ? 'data:font/truetype;charset=utf-8;base64,' . base64_encode(file_get_contents($fontPath))
            : '';

        $sebutan_peserta = (strtolower($peserta->tingkat_pendidikan) === 'smk'
                        || strtolower($peserta->tingkat_pendidikan) === 'slta')
            ? 'Siswa/i' : 'Mahasiswa/i';

        Carbon::setLocale('id');

        $sertifikat_data = $peserta->sertifikat_data ? json_decode($peserta->sertifikat_data, true) : [];

        return array_merge($assets, [
            'peserta'          => $peserta,
            'sebutan_peserta'  => $sebutan_peserta,
            'nomor_sertifikat' => $sertifikat_data['nomor_sertifikat'] ?? '',
            'predikat'         => $sertifikat_data['predikat'] ?? '',
            'tanggal_mulai'    => Carbon::parse($peserta->tanggal_mulai)->translatedFormat('d F Y'),
            'tanggal_selesai'  => Carbon::parse($peserta->tanggal_selesai)->translatedFormat('d F Y'),
            'tanggal_terbit'   => Carbon::parse($peserta->tanggal_selesai)->translatedFormat('d F Y'),
        ]);
    }

    public function previewSertifikat($id)
    {
        $peserta = PesertaMagang::findOrFail($id);

        $data = $this->buildSertifikatData($peserta);
        $data['is_pdf'] = false;

        $html = view('admin.dokumen.template-sertifikat', $data)->render();
        return response($html)->header('Content-Type', 'text/html');
    }

    public function downloadSertifikat($id)
    {
        $peserta = PesertaMagang::findOrFail($id);

        $data = $this->buildSertifikatData($peserta);
        $data['is_pdf'] = true;

        $pdf = Pdf::loadView('admin.dokumen.template-sertifikat', $data)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'Times',
                'dpi'                  => 150,
            ]);

        $filename = 'Sertifikat_Magang_' . str_replace(' ', '_', $peserta->nama) . '.pdf';
        return $pdf->download($filename);
    }

    public function uploadDanKirimSertifikat(Request $request, $id)
    {
        $request->validate([
            'surat_ttd'   => 'required|mimes:pdf|max:5120',
            'pesan_email' => 'nullable|string',
        ]);

        $peserta = PesertaMagang::findOrFail($id);

        // Upload File
        if ($request->hasFile('surat_ttd')) {
            $file     = $request->file('surat_ttd');
            $filename = 'Sertifikat_TTD_' . time() . '_' . str_replace(' ', '_', $peserta->nama) . '.pdf';
            $path     = $file->storeAs('public/sertifikat', $filename);

            $peserta->surat_sertifikat   = 'sertifikat/' . $filename;
            $peserta->is_sertifikat_sent = 1;

            // Cek apakah semua dokumen sudah selesai → ubah status ke Selesai
            if ($peserta->is_sk_sent && $peserta->is_evaluasi_sent && $peserta->is_sertifikat_sent) {
                $peserta->status_magang = 'Selesai';
            }

            $peserta->save();
        }

        // Kirim Email
        try {
            Mail::send('emails.sertifikat', [
                'peserta'         => $peserta,
                'pesan_tambahan'  => $request->pesan_email,
            ], function ($message) use ($peserta) {
                $message->to($peserta->email)
                        ->subject('Sertifikat Magang PUSDATIN PUPR');

                if ($peserta->surat_sertifikat && Storage::disk('public')->exists($peserta->surat_sertifikat)) {
                    $message->attach(Storage::disk('public')->path($peserta->surat_sertifikat), [
                        'as'   => 'Sertifikat_Magang_' . $peserta->nama . '.pdf',
                        'mime' => 'application/pdf',
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Sertifikat berhasil diunggah dan dikirim ke email peserta.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage(),
            ], 500);
        }
    }
}
