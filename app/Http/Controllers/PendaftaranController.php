<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimKerja; // Wajib dipanggil agar bisa baca database Tim Kerja

class PendaftaranController extends Controller
{
    // Fungsi untuk menampilkan halaman form pendaftaran
    public function create()
    {
        // Ambil data tim kerja + hitung jumlah peserta aktif di masing-masing tim
        $timKerja = TimKerja::withCount([
            'pesertaMagang' => function ($query) {
                $query->where('status_magang', 'Aktif');
            }
        ])->get();

        // Kelompokkan berdasarkan kolom 'bidang'
        $groupedTimKerja = $timKerja->groupBy('bidang');

        // Kirim data ke file Blade (resources/views/pendaftaran/form.blade.php)
        return view('pendaftaran.form', compact('groupedTimKerja'));
    }

    // Fungsi untuk memproses data saat tombol "Kirim" ditekan (Kita bahas nanti)
    public function store(Request $request)
    {
        // ... (Kode validasi dan simpan data ke database akan ditaruh di sini) ...
    }
}