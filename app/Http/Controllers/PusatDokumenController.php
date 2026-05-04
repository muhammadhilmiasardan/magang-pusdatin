<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PesertaMagang;
use Carbon\Carbon;

class PusatDokumenController extends Controller
{
    public function index()
    {
        // 1. SK Magang (Peserta Aktif yang belum dikirim SK)
        // Menurut spek: Berisi daftar peserta `Aktif` untuk dicetakkan SK Magang.
        // Bisa juga yang belum dikirim SK. Kita ambil yang statusnya Aktif.
        $skMagang = PesertaMagang::where('status_magang', 'Aktif')->get();

        // 2. Evaluasi H-7 s/d H-14
        // Hanya menampilkan peserta dengan sisa waktu magang 7-14 hari
        // Artinya: tanggal_selesai antara hari_ini + 7 hari sampai hari_ini + 14 hari
        $today = Carbon::today();
        $evaluasi = PesertaMagang::where('status_magang', 'Aktif')
            ->whereBetween('tanggal_selesai', [$today->copy()->addDays(7), $today->copy()->addDays(14)])
            ->get();

        // 3. Sertifikat Kelulusan
        // Menampilkan daftar alumni magang (Selesai) yang belum mendapatkan sertifikat
        $sertifikat = PesertaMagang::where('status_magang', 'Selesai')
            ->where('is_sertifikat_sent', 0)
            ->get();

        return view('admin.dokumen.index', compact('skMagang', 'evaluasi', 'sertifikat'));
    }
}
