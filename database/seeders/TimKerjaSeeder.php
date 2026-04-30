<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimKerjaSeeder extends Seeder
{
    /**
     * Seed data tim kerja berdasarkan struktur organisasi PUSDATIN PUPR.
     */
    public function run(): void
    {
        $data = [
            // MANAJEMEN TEKNOLOGI INFORMASI
            ['nama_tim' => 'Tim Kerja Tata Kelola dan Perizinan', 'bidang' => 'Manajemen Teknologi Informasi', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Infrastruktur / Teknologi Informasi', 'bidang' => 'Manajemen Teknologi Informasi', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Sistem Informasi', 'bidang' => 'Manajemen Teknologi Informasi', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Korespondensi dan Kolaborasi', 'bidang' => 'Manajemen Teknologi Informasi', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Sistem Kendali Otomatis dan Kecerdasan Buatan', 'bidang' => 'Manajemen Teknologi Informasi', 'kuota_maksimal' => 5],

            // BIDANG DATA ANALITIK PEKERJAAN UMUM
            ['nama_tim' => 'Tim Layanan dan Integrasi Data Infrastruktur', 'bidang' => 'Bidang Data Analitik Pekerjaan Umum', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Digitalisasi Infrastruktur Pekerjaan Umum', 'bidang' => 'Bidang Data Analitik Pekerjaan Umum', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Sistem Informasi Geografis dan Portal GIS PUPR', 'bidang' => 'Bidang Data Analitik Pekerjaan Umum', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Data dan Informasi Statistik Infrastruktur dan Kebencanaan', 'bidang' => 'Bidang Data Analitik Pekerjaan Umum', 'kuota_maksimal' => 5],

            // BIDANG PENGELOLAAN DATA BENCANA INFRASTRUKTUR
            ['nama_tim' => 'Tim Kerja Analisis Data dan Informasi Geospasial Infrastruktur dan Kebencanaan', 'bidang' => 'Bidang Pengelolaan Data Bencana Infrastruktur', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Pelaporan Kebencanaan', 'bidang' => 'Bidang Pengelolaan Data Bencana Infrastruktur', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Manajemen Data dan Bencana', 'bidang' => 'Bidang Pengelolaan Data Bencana Infrastruktur', 'kuota_maksimal' => 5],

            // SUBBAGIAN TATA USAHA
            ['nama_tim' => 'Tim Kerja Kepegawaian dan Jabatan Fungsional', 'bidang' => 'Subbagian Tata Usaha', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Keuangan', 'bidang' => 'Subbagian Tata Usaha', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja BMN dan Arsip', 'bidang' => 'Subbagian Tata Usaha', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Monitoring dan Evaluasi', 'bidang' => 'Subbagian Tata Usaha', 'kuota_maksimal' => 5],
            ['nama_tim' => 'Tim Kerja Sarana dan Prasarana Perkantoran', 'bidang' => 'Subbagian Tata Usaha', 'kuota_maksimal' => 5],
        ];

        foreach ($data as $tim) {
            DB::table('tim_kerja')->insert(array_merge($tim, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
