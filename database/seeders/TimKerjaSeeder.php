<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimKerjaSeeder extends Seeder
{
    /**
     * Seed data tim kerja berdasarkan struktur organisasi PUSDATIN PUPR.
     * Data terbaru per 4 Mei 2026. Kuota: 5 orang per tim kerja.
     */
    public function run(): void
    {
        $data = [
            // =====================================================
            // BIDANG MANAJEMEN TEKNOLOGI INFORMASI (MTI)
            // =====================================================
            ['nama_tim' => 'Tim Kerja Tata Kelola dan Perizinan', 'bidang' => 'Bidang Manajemen Teknologi Informasi', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Keamanan Teknologi Informasi', 'bidang' => 'Bidang Manajemen Teknologi Informasi', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Infrastruktur Teknologi Informasi', 'bidang' => 'Bidang Manajemen Teknologi Informasi', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Sistem Informasi', 'bidang' => 'Bidang Manajemen Teknologi Informasi', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Korespondensi dan Kolaborasi', 'bidang' => 'Bidang Manajemen Teknologi Informasi', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Sistem Kendali Otomatis dan Kecerdasan Buatan', 'bidang' => 'Bidang Manajemen Teknologi Informasi', 'kuota_maksimal' => 5],

            // =====================================================
            // BIDANG DATA ANALITIK PEKERJAAN UMUM (DA-PU)
            // =====================================================
            ['nama_tim' => 'Tim Kerja Layanan dan Integrasi Data Infrastruktur', 'bidang' => 'Bidang Data Analitik Pekerjaan Umum', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Sistem Informasi Geografis dan Portal GIS PU', 'bidang' => 'Bidang Data Analitik Pekerjaan Umum', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Data dan Informasi Statistik Infrastruktur Pekerjaan Umum', 'bidang' => 'Bidang Data Analitik Pekerjaan Umum', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Digitalisasi Infrastruktur Pekerjaan Umum', 'bidang' => 'Bidang Data Analitik Pekerjaan Umum', 'kuota_maksimal' => 5],

            // =====================================================
            // BIDANG PENGELOLAAN DATA BENCANA INFRASTRUKTUR (PDBI)
            // =====================================================
            ['nama_tim' => 'Tim Kerja Analisis Data dan Informasi Geospasial Infrastruktur dan Kebencanaan', 'bidang' => 'Bidang Pengelolaan Data Bencana Infrastruktur', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Pelaporan Kebencanaan', 'bidang' => 'Bidang Pengelolaan Data Bencana Infrastruktur', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Manajemen Data dan Bencana', 'bidang' => 'Bidang Pengelolaan Data Bencana Infrastruktur', 'kuota_maksimal' => 5],

            // =====================================================
            // SUBBAGIAN TATA USAHA (TU)
            // =====================================================
            ['nama_tim' => 'Tim Kerja Kepegawaian dan Jabatan Fungsional', 'bidang' => 'Subbagian Tata Usaha', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Keuangan', 'bidang' => 'Subbagian Tata Usaha', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Pengelolaan BMN dan Arsip', 'bidang' => 'Subbagian Tata Usaha', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Monitoring dan Evaluasi', 'bidang' => 'Subbagian Tata Usaha', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Sarana dan Prasarana Perkantoran', 'bidang' => 'Subbagian Tata Usaha', 'kuota_maksimal' => 5],
        ];

        foreach ($data as $tim) {
            DB::table('tim_kerja')->updateOrInsert(
                ['nama_tim' => $tim['nama_tim']],
                array_merge($tim, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
