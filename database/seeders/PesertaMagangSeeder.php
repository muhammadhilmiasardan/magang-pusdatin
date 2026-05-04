<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TimKerja;
use App\Models\PesertaMagang;
use Carbon\Carbon;

class PesertaMagangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = base_path('data orang magang.csv');

        if (!file_exists($csvFile)) {
            $this->command->warn("File $csvFile tidak ditemukan.");
            return;
        }

        $file = fopen($csvFile, 'r');
        $header = fgetcsv($file, 0, ';'); // Assuming semicolon separated based on sample

        // Peta penempatan dari CSV ke ID tim kerja
        // BDI -> Bidang Data Analitik Pekerjaan Umum
        // MTI -> Manajemen Teknologi Informasi
        // TU -> Subbagian Tata Usaha
        // Karena CSV tidak menyebut nama tim kerja spesifik, kita pilih tim kerja pertama dari bidang tersebut secara acak atau berurutan
        
        $timKerjaMap = [
            'BDI' => TimKerja::where('bidang', 'Bidang Data Analitik Pekerjaan Umum')->pluck('id')->toArray(),
            'MTI' => TimKerja::where('bidang', 'Manajemen Teknologi Informasi')->pluck('id')->toArray(),
            'TU' => TimKerja::where('bidang', 'Subbagian Tata Usaha')->pluck('id')->toArray(),
        ];

        while (($row = fgetcsv($file, 0, ';')) !== false) {
            if (count($row) < 11) continue; // Pastikan baris memiliki jumlah kolom yang benar

            $nama = $row[1];
            $tingkatPendidikan = $row[3];
            $namaInstitusi = $row[4];
            $jurusan = $row[5];
            $tanggalMulai = $row[6];
            $tanggalSelesai = $row[7];
            $nomorTelp = $row[8];
            $penempatan = $row[9];
            $statusMagang = $row[10];

            if (empty($nama)) continue;

            // Tentukan tim kerja berdasarkan penempatan (pilih acak dari tim yang tersedia di bidang tsb)
            $idTimKerja1 = null;
            if (isset($timKerjaMap[$penempatan]) && count($timKerjaMap[$penempatan]) > 0) {
                $idTimKerja1 = $timKerjaMap[$penempatan][array_rand($timKerjaMap[$penempatan])];
            } else {
                // Default fallback jika penempatan tidak dikenali
                $idTimKerja1 = TimKerja::first()->id ?? 1;
            }

            // Pilihan 2 bebas, tapi usahakan beda
            $idTimKerja2 = TimKerja::where('id', '!=', $idTimKerja1)->inRandomOrder()->first()->id ?? $idTimKerja1;

            PesertaMagang::create([
                'nama' => $nama,
                'tingkat_pendidikan' => $tingkatPendidikan,
                'nama_institusi' => $namaInstitusi,
                'jurusan' => $jurusan,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'nomor_telp' => $nomorTelp == '-' ? '08000000000' : $nomorTelp,
                'email' => strtolower(str_replace(' ', '.', $nama)) . '@gmail.com', // fake email
                'email_institusi' => 'kampus@' . strtolower(str_replace(' ', '', $namaInstitusi)) . '.ac.id', // fake email
                'id_tim_kerja_1' => $idTimKerja1,
                'id_tim_kerja_2' => $idTimKerja2,
                'cv' => null,
                'surat_rekomendasi' => 'surat_dummy.pdf',
                'status_magang' => $statusMagang,
                'is_sk_sent' => $statusMagang == 'Selesai' || $statusMagang == 'Aktif' ? 1 : 0,
                'is_evaluasi_sent' => $statusMagang == 'Selesai' ? 1 : 0,
                'is_sertifikat_sent' => $statusMagang == 'Selesai' ? 1 : 0, // Assume 1 for old data, we can tweak this
            ]);
        }

        fclose($file);
    }
}
