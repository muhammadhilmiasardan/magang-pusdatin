<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PesertaMagang;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ArsipDokumenController extends Controller
{
    public function index()
    {
        // Get all participants who have at least one finalized document
        $peserta = PesertaMagang::whereNotNull('surat_penerimaan_final')
            ->orWhereNotNull('surat_keterangan')
            ->orWhereNotNull('surat_evaluasi')
            ->orWhereNotNull('surat_sertifikat')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.arsip.index', compact('peserta'));
    }

    public function downloadBulk(Request $request)
    {
        $request->validate([
            'peserta_ids' => 'required|array',
            'peserta_ids.*' => 'exists:peserta_magang,id',
        ]);

        $pesertaIds = $request->peserta_ids;
        $peserta = PesertaMagang::whereIn('id', $pesertaIds)->get();

        if ($peserta->isEmpty()) {
            return back()->with('error', 'Tidak ada peserta yang dipilih.');
        }

        $zipFileName = 'Arsip_Dokumen_Magang_' . time() . '.zip';
        $zipFilePath = storage_path('app/public/' . $zipFileName);

        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $hasFiles = false;

            foreach ($peserta as $p) {
                // Name formatting: [DocumentType] - [Name] - [Institution].pdf
                // Clean the name to be safe for filenames
                $safeName = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '', $p->nama);
                $safeInst = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '', $p->nama_institusi);
                $baseName = "{$safeName} - {$safeInst}";

                $documents = [
                    'Surat_Penerimaan' => $p->surat_penerimaan_final,
                    'SK_Magang' => $p->surat_keterangan,
                    'Surat_Evaluasi' => $p->surat_evaluasi,
                    'Sertifikat' => $p->surat_sertifikat,
                ];

                foreach ($documents as $type => $filePath) {
                    if ($filePath && Storage::disk('public')->exists($filePath)) {
                        $fileToZip = Storage::disk('public')->path($filePath);
                        $zipName = "{$type} - {$baseName}.pdf";
                        $zip->addFile($fileToZip, $zipName);
                        $hasFiles = true;
                    }
                }
            }

            $zip->close();

            if (!$hasFiles) {
                if (file_exists($zipFilePath)) {
                    unlink($zipFilePath);
                }
                return back()->with('error', 'Peserta yang dipilih tidak memiliki dokumen arsip.');
            }

            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        } else {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }
    }
}
