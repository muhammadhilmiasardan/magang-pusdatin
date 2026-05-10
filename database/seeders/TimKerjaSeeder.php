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
            ['nama_tim' => 'Tim Kerja Tata Kelola dan Perizinan', 'bidang' => 'Bidang Manajemen Teknologi Informasi', 'kuota_maksimal' => 5, 'ketua_tim' => 'Widiastuti Kusumo Wardhani, S.Kom', 'nip_ketua_tim' => '198508232008122001'],

            ['nama_tim' => 'Tim Kerja Infrastruktur Teknologi Informasi', 'bidang' => 'Bidang Manajemen Teknologi Informasi', 'kuota_maksimal' => 5, 'ketua_tim' => 'Rio Rasian Anggorobekti, S.Kom., M.T.I.', 'nip_ketua_tim' => '198309112010121003'],
            ['nama_tim' => 'Tim Kerja Sistem Informasi', 'bidang' => 'Bidang Manajemen Teknologi Informasi', 'kuota_maksimal' => 5, 'ketua_tim' => 'Falih Farhan, S.Kom', 'nip_ketua_tim' => '199711092022031008'],
            ['nama_tim' => 'Tim Kerja Korespondensi dan Kolaborasi', 'bidang' => 'Bidang Manajemen Teknologi Informasi', 'kuota_maksimal' => 5, 'ketua_tim' => 'Ramadhan Afwan Mutasodirin, S.Kom', 'nip_ketua_tim' => '199812282022031004'],
            ['nama_tim' => 'Tim Kerja Sistem Kendali Otomatis dan Kecerdasan Buatan', 'bidang' => 'Bidang Manajemen Teknologi Informasi', 'kuota_maksimal' => 5, 'ketua_tim' => 'Jimmy Segers, S.T.', 'nip_ketua_tim' => '197704192008121001'],

            // =====================================================
            // BIDANG DATA ANALITIK PEKERJAAN UMUM (DA-PU)
            // =====================================================
            ['nama_tim' => 'Tim Kerja Layanan dan Integrasi Data Infrastruktur', 'bidang' => 'Bidang Data Analitik Pekerjaan Umum', 'kuota_maksimal' => 5, 'ketua_tim' => 'Gama Ilmy Hartanto, S. Kom', 'nip_ketua_tim' => '198206262010121004'],
            ['nama_tim' => 'Tim Kerja Sistem Informasi Geografis dan Portal GIS PU', 'bidang' => 'Bidang Data Analitik Pekerjaan Umum', 'kuota_maksimal' => 5, 'ketua_tim' => 'Irghan Santiz Ruqi Antabuana, ST', 'nip_ketua_tim' => '199602102019031009'],
            ['nama_tim' => 'Tim Kerja Data dan Informasi Statistik Infrastruktur Pekerjaan Umum', 'bidang' => 'Bidang Data Analitik Pekerjaan Umum', 'kuota_maksimal' => 5, 'ketua_tim' => 'Mayta Utari, S.Si', 'nip_ketua_tim' => '199405072022032006'],
            ['nama_tim' => 'Tim Kerja Digitalisasi Infrastruktur Pekerjaan Umum', 'bidang' => 'Bidang Data Analitik Pekerjaan Umum', 'kuota_maksimal' => 5, 'ketua_tim' => 'Muhammad Ihsan, ST', 'nip_ketua_tim' => '199409012019031006'],

            // =====================================================
            // BIDANG PENGELOLAAN DATA BENCANA INFRASTRUKTUR (PDBI)
            // =====================================================
            ['nama_tim' => 'Tim Kerja Analisis Data dan Informasi Geospasial Infrastruktur dan Kebencanaan', 'bidang' => 'Bidang Pengelolaan Data Bencana Infrastruktur', 'kuota_maksimal' => 5, 'ketua_tim' => 'Amalia Siti Rohmah, ST., M.Eng', 'nip_ketua_tim' => '198709272010122003'],
            ['nama_tim' => 'Tim Kerja Pelaporan Kebencanaan', 'bidang' => 'Bidang Pengelolaan Data Bencana Infrastruktur', 'kuota_maksimal' => 5, 'ketua_tim' => 'Sofyan Nurhadi, S.Si', 'nip_ketua_tim' => '198709212024211001'],
            ['nama_tim' => 'Tim Kerja Manajemen Data dan Bencana', 'bidang' => 'Bidang Pengelolaan Data Bencana Infrastruktur', 'kuota_maksimal' => 5, 'ketua_tim' => 'Adi Surya, S. Kom', 'nip_ketua_tim' => '197511272008121001'],

            // =====================================================
            // SUBBAGIAN TATA USAHA (TU)
            // =====================================================
            ['nama_tim' => 'Tim Kerja Kepegawaian dan Jabatan Fungsional', 'bidang' => 'Subbagian Tata Usaha', 'kuota_maksimal' => 5, 'ketua_tim' => 'Devy Wardhani, SE, MH', 'nip_ketua_tim' => '198509232009122002'],
            ['nama_tim' => 'Tim Kerja Keuangan', 'bidang' => 'Subbagian Tata Usaha', 'kuota_maksimal' => 5, 'ketua_tim' => 'Astrianti, SE', 'nip_ketua_tim' => '198807082010122001'],
            ['nama_tim' => 'Tim Kerja Pengelolaan BMN dan Arsip', 'bidang' => 'Subbagian Tata Usaha', 'kuota_maksimal' => 5, 'ketua_tim' => 'Sri Wulandari Dwi Wahyuni, SM', 'nip_ketua_tim' => '198402162010122002'],
            ['nama_tim' => 'Tim Kerja Monitoring dan Evaluasi', 'bidang' => 'Subbagian Tata Usaha', 'kuota_maksimal' => 5, 'ketua_tim' => 'Fitri Fatimah, A. Md', 'nip_ketua_tim' => '198903122010122002'],
            ['nama_tim' => 'Tim Kerja Sarana dan Prasarana Perkantoran', 'bidang' => 'Subbagian Tata Usaha', 'kuota_maksimal' => 5, 'ketua_tim' => 'Supram, A.Md', 'nip_ketua_tim' => '196912091995031003'],
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
