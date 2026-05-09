<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimKerja;
use App\Models\PesertaMagang;

class PendaftaranController extends Controller
{
    // Fungsi untuk menampilkan halaman form pendaftaran
    public function create()
    {
        // Ambil data tim kerja + hitung peserta yang sudah diterima (Belum Aktif + Aktif)
        // Kedua status ini sudah mengurangi kuota karena peserta telah resmi diterima
        $timKerja = TimKerja::withCount([
            'pesertaMagang' => function ($query) {
                $query->whereIn('status_magang', ['Belum Aktif', 'Aktif']);
            }
        ])->get();

        // Kelompokkan berdasarkan kolom 'bidang'
        $groupedTimKerja = $timKerja->groupBy('bidang');

        // Kirim data ke file Blade (resources/views/pendaftaran/form.blade.php)
        return view('pendaftaran.form', compact('groupedTimKerja'));
    }

    // Fungsi untuk memproses data saat tombol "Kirim" ditekan
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nama'                => 'required|string|max:255',
            'tingkat_pendidikan'  => 'required|in:Universitas,SLTA',
            'nama_institusi'      => 'required|string|max:255',
            'jurusan'             => 'required|string|max:255',
            'nim_nis'             => 'required|string|max:50',
            'tanggal_mulai'       => 'required|date',
            'tanggal_selesai'     => 'required|date|after:tanggal_mulai',
            'nomor_telp'          => 'required|string|max:20',
            'email'               => 'required|email|max:255',
            'email_institusi'     => 'required|email|max:255',
            'bidang'              => 'required|string',
            'id_tim_kerja_1'      => 'required|exists:tim_kerja,id',
            'id_tim_kerja_2'      => 'required|exists:tim_kerja,id|different:id_tim_kerja_1',
            'surat_rekomendasi'   => 'required|file|mimes:pdf|max:2048',
            'cv'                  => 'nullable|file|mimes:pdf|max:2048',
            'pas_foto'            => 'required|image|mimes:jpg,jpeg,png|max:1024',
        ], [
            'tanggal_selesai.after'     => 'Tanggal selesai harus setelah tanggal mulai.',
            'id_tim_kerja_2.different'  => 'Pilihan Tim Kerja 2 harus berbeda dari Pilihan 1.',
            'surat_rekomendasi.required'=> 'Surat Permohonan Magang wajib dilampirkan.',
            'surat_rekomendasi.max'     => 'Ukuran Surat Permohonan maksimal 2MB.',
            'cv.max'                    => 'Ukuran CV maksimal 2MB.',
            'pas_foto.max'              => 'Ukuran Pas Foto maksimal 1MB.',
        ]);

        // Upload Surat Permohonan Magang (wajib)
        $suratPath = $request->file('surat_rekomendasi')->store('dokumen/surat-permohonan', 'public');

        // Upload CV (opsional)
        $cvPath = null;
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('dokumen/cv', 'public');
        }

        // Upload Pas Foto (wajib)
        $fotoPath = $request->file('pas_foto')->store('dokumen/pas-foto', 'public');

        // Simpan ke database
        PesertaMagang::create([
            'nama'               => $validated['nama'],
            'tingkat_pendidikan' => $validated['tingkat_pendidikan'],
            'nama_institusi'     => $validated['nama_institusi'],
            'jurusan'            => $validated['jurusan'],
            'nim_nis'            => $validated['nim_nis'],
            'tanggal_mulai'      => $validated['tanggal_mulai'],
            'tanggal_selesai'    => $validated['tanggal_selesai'],
            'nomor_telp'         => $validated['nomor_telp'],
            'email'              => $validated['email'],
            'email_institusi'    => $validated['email_institusi'],
            'id_tim_kerja_1'     => $validated['id_tim_kerja_1'],
            'id_tim_kerja_2'     => $validated['id_tim_kerja_2'],
            'surat_rekomendasi'  => $suratPath,
            'cv'                 => $cvPath,
            'pas_foto'           => $fotoPath,
            'status_magang'      => 'Menunggu Review',
            'is_sk_sent'         => 0,
            'is_evaluasi_sent'   => 0,
            'is_sertifikat_sent' => 0,
        ]);

        return redirect()->route('pendaftaran.sukses');
    }
}