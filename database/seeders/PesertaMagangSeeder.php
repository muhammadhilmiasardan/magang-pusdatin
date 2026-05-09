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
     * Mapping dari value Penempatan di CSV ke nama_tim di tabel tim_kerja.
     * 
     * Key   = value persis dari kolom Penempatan CSV (setelah trim & normalisasi)
     * Value = 'nama_tim' di tabel tim_kerja, ATAU 'bidang:NamaBidang' untuk random tim dari bidang tsb
     */
    private function getPenempatanMap(): array
    {
        return [
            // ──── SINGKATAN BIDANG (ambil random tim dari bidang) ────
            'BDI'   => 'bidang:Bidang Data Analitik Pekerjaan Umum',
            'BDA'   => 'bidang:Bidang Data Analitik Pekerjaan Umum',
            'DI'    => 'bidang:Bidang Data Analitik Pekerjaan Umum',
            'MTI'   => 'bidang:Bidang Manajemen Teknologi Informasi',
            'TU'    => 'bidang:Subbagian Tata Usaha',

            // ──── SINGKATAN + NAMA TIM SPESIFIK ────
            'BDI - BIM dan Pengukuran Referensi'  => 'Tim Kerja Digitalisasi Infrastruktur Pekerjaan Umum',
            'BDI - BIM dan GIS'                    => 'Tim Kerja Sistem Informasi Geografis dan Portal GIS PU',
            'BDA-BIM'                              => 'Tim Kerja Digitalisasi Infrastruktur Pekerjaan Umum',
            'TU & MTI'                             => 'bidang:Subbagian Tata Usaha',
            'TU - Tim Kerja Kepegawaian dan JF'    => 'Tim Kerja Kepegawaian dan Jabatan Fungsional',
            'Sistem Informasi - MTI'               => 'Tim Kerja Sistem Informasi',

            // ──── NAMA TIM KERJA LENGKAP (match langsung / redirect ke nama baru) ────
            'Tim Kerja Sistem Kendali Otomatis dan Kecerdasan Buatan' => 'Tim Kerja Sistem Kendali Otomatis dan Kecerdasan Buatan',
            'Tim Kerja Jaringan Teknologi Informasi'                   => 'Tim Kerja Infrastruktur Teknologi Informasi',
            'Tim Kerja Sistem IOT'                                     => 'Tim Kerja Keamanan Teknologi Informasi',
            'Tim Kerja Sistem Informasi'                               => 'Tim Kerja Sistem Informasi',
            'Tim Kerja Kepegawaian dan Jabatan Fungsional'             => 'Tim Kerja Kepegawaian dan Jabatan Fungsional',
            'Tim Kerja Keuangan'                                       => 'Tim Kerja Keuangan',
            'Tim Kerja BMN dan Arsip'                                  => 'Tim Kerja Pengelolaan BMN dan Arsip',
            'Tim Kerja Pengelolaan BMN dan Arsip'                      => 'Tim Kerja Pengelolaan BMN dan Arsip',
            'Tim Kerja Program dan Evaluasi'                           => 'Tim Kerja Monitoring dan Evaluasi',
            'Tim Kerja Monitoring dan Evaluasi'                        => 'Tim Kerja Monitoring dan Evaluasi',
            'Tim Kerja Pelaporan Kebencanaan'                          => 'Tim Kerja Pelaporan Kebencanaan',
            'Tim Kerja Manajemen Data dan Bencana'                     => 'Tim Kerja Manajemen Data dan Bencana',
            'Tim Kerja Digitalisasi Infrastruktur Pekerjaan Umum'      => 'Tim Kerja Digitalisasi Infrastruktur Pekerjaan Umum',
            'Tim Kerja Sistem Informasi Geografis dan Portal GIS PUPR' => 'Tim Kerja Sistem Informasi Geografis dan Portal GIS PU',
            'Tim Kerja Sistem Informasi Geografis dan Portal GIS PU'   => 'Tim Kerja Sistem Informasi Geografis dan Portal GIS PU',
            'Tim Kerja Analisis Data dan Informasi Geospasial Infrastruktur dan Kebencanaan' => 'Tim Kerja Analisis Data dan Informasi Geospasial Infrastruktur dan Kebencanaan',
            'Tim Kerja Layanan dan Integrasi Data Infrastruktur'       => 'Tim Kerja Layanan dan Integrasi Data Infrastruktur',
            'Tim Kerja Data dan Informasi Statistik Infrastruktur dan Kebencanaan' => 'Tim Kerja Data dan Informasi Statistik Infrastruktur Pekerjaan Umum',
            'Tim Kerja Data dan Informasi Statistik Infrastruktur Pekerjaan Umum' => 'Tim Kerja Data dan Informasi Statistik Infrastruktur Pekerjaan Umum',
            'Tim Kerja Tata Kelola dan Perizinan'                      => 'Tim Kerja Tata Kelola dan Perizinan',
            'Tim Kerja Korespondensi dan Kolaborasi'                   => 'Tim Kerja Korespondensi dan Kolaborasi',
            'Tim Kerja Keamanan Teknologi Informasi'                   => 'Tim Kerja Keamanan Teknologi Informasi',
            'Tim Kerja Infrastruktur Teknologi Informasi'              => 'Tim Kerja Infrastruktur Teknologi Informasi',
            'Tim Kerja Sarana dan Prasarana Perkantoran'                => 'Tim Kerja Sarana dan Prasarana Perkantoran',

            // ──── NAMA TIM KERJA + SUFFIX BIDANG ────
            'Tim Kerja Pelaporan Kebencanaan - PDBI'                   => 'Tim Kerja Pelaporan Kebencanaan',
            'PDBI - Tim Kerja Pelaporan Kebencanaan'                   => 'Tim Kerja Pelaporan Kebencanaan',
            'Tim Kerja Analisis Data dan Informasi Geospasial Infrastruktur dan Kebencanaan - Bidang PDBI' => 'Tim Kerja Analisis Data dan Informasi Geospasial Infrastruktur dan Kebencanaan',
            'Tim Kerja Layanan dan Integrasi Data Infrastruktur - DA'  => 'Tim Kerja Layanan dan Integrasi Data Infrastruktur',
            'DA - Tim Kerja Data dan Informasi Statistik Infrastruktur Pekerjaan Umum' => 'Tim Kerja Data dan Informasi Statistik Infrastruktur Pekerjaan Umum',
            'Tim Kerja Kepegawaian dan Jabatan Fungsional,'            => 'Tim Kerja Kepegawaian dan Jabatan Fungsional',

            // ──── NAMA INFORMAL (Nickname) → map ke tim terdekat ────
            'Tim Jaringan'       => 'Tim Kerja Infrastruktur Teknologi Informasi',
            'Tim Kepegawaian JF' => 'Tim Kerja Kepegawaian dan Jabatan Fungsional',
            'Manajemen Data dan Bencana' => 'Tim Kerja Manajemen Data dan Bencana',
            'Tim Irghan'         => 'Tim Kerja Analisis Data dan Informasi Geospasial Infrastruktur dan Kebencanaan',
            'Tim Mba Amel'       => 'Tim Kerja Analisis Data dan Informasi Geospasial Infrastruktur dan Kebencanaan',
        ];
    }

    /**
     * Normalisasi string penempatan dari CSV:
     * - Hapus karakter non-printable / Unicode artifacts
     * - Trim whitespace dan newlines
     * - Hapus trailing comma
     */
    private function normalizePenempatan(string $raw): string
    {
        // Hapus karakter non-printable (kontrol Unicode, BOM, dll)
        $clean = preg_replace('/[^\x20-\x7E\x{00C0}-\x{024F}]/u', '', $raw);
        if ($clean === null) {
            $clean = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $raw);
        }
        // Gabungkan multi-line jadi satu baris
        $clean = preg_replace('/\s+/', ' ', $clean);
        // Trim dan hapus trailing comma
        $clean = rtrim(trim($clean), ',');
        return $clean;
    }

    /**
     * Resolve penempatan CSV menjadi ID tim kerja di database.
     */
    private function resolveTimKerjaId(string $penempatan, array $map, array &$timKerjaCache): ?int
    {
        $normalized = $this->normalizePenempatan($penempatan);

        if (!isset($map[$normalized])) {
            $this->command->warn("  ⚠ Penempatan tidak dikenal: [{$normalized}] (raw: [{$penempatan}])");
            return null;
        }

        $target = $map[$normalized];

        // Jika target dimulai dengan 'bidang:', ambil random tim dari bidang tersebut
        if (str_starts_with($target, 'bidang:')) {
            $bidang = substr($target, 7);
            if (!isset($timKerjaCache['bidang'][$bidang])) {
                $timKerjaCache['bidang'][$bidang] = TimKerja::where('bidang', $bidang)->pluck('id')->toArray();
            }
            $ids = $timKerjaCache['bidang'][$bidang];
            return !empty($ids) ? $ids[array_rand($ids)] : null;
        }

        // Jika target adalah nama tim spesifik
        if (!isset($timKerjaCache['tim'][$target])) {
            $tim = TimKerja::where('nama_tim', $target)->first();
            $timKerjaCache['tim'][$target] = $tim ? $tim->id : null;
            if (!$tim) {
                $this->command->warn("  ⚠ Tim kerja tidak ditemukan di DB: [{$target}]");
            }
        }
        return $timKerjaCache['tim'][$target];
    }

    /**
     * Normalisasi nomor telepon dari CSV.
     */
    private function normalizePhone(string $raw): string
    {
        if (empty($raw) || $raw === '-') {
            return '08000000000';
        }

        // Hapus semua karakter non-angka
        $digits = preg_replace('/[^0-9]/', '', $raw);

        // Jika format scientific notation dari Excel (6,28953E+13 → sudah di-parse jadi angka panjang)
        // Cek apakah raw mengandung 'E+' (scientific notation)
        if (stripos($raw, 'E+') !== false || stripos($raw, 'e+') !== false) {
            // Parse scientific notation
            $number = number_format((float)str_replace(',', '.', $raw), 0, '', '');
            $digits = $number;
        }

        // Pastikan diawali 0
        if (!str_starts_with($digits, '0') && strlen($digits) >= 10) {
            $digits = '0' . $digits;
        }

        return $digits ?: '08000000000';
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = base_path('anak magang.csv');

        if (!file_exists($csvFile)) {
            $this->command->warn("File $csvFile tidak ditemukan.");
            return;
        }

        $file = fopen($csvFile, 'r');
        $header = fgetcsv($file, 0, ';');
        $this->command->info("CSV Header: " . implode(' | ', $header));

        $map = $this->getPenempatanMap();
        $timKerjaCache = ['bidang' => [], 'tim' => []];
        $successCount = 0;
        $skipCount = 0;
        $failCount = 0;

        while (($row = fgetcsv($file, 0, ';')) !== false) {
            // Pastikan baris memiliki minimal 14 kolom (No sampai Status)
            if (count($row) < 14) {
                $skipCount++;
                continue;
            }

            $nama = trim($row[1] ?? '');
            $nim_nis = trim($row[2] ?? '');
            $tingkatPendidikan = trim($row[3] ?? '');
            $namaInstitusi = trim($row[4] ?? '');
            $jurusan = trim($row[5] ?? '');
            $tanggalMulai = trim($row[6] ?? '');
            $tanggalSelesai = trim($row[7] ?? '');
            $nomorTelp = trim($row[8] ?? '');
            $emailRaw = trim($row[9] ?? '');
            $emailInstitusiRaw = trim($row[10] ?? '');
            $bidang = trim($row[11] ?? '');
            $timKerja = trim($row[12] ?? '');
            $statusMagang = trim($row[13] ?? 'Aktif');

            $penempatan = !empty($timKerja) ? $timKerja : $bidang;

            // Skip baris kosong
            if (empty($nama) || strtolower($nama) === 'nama' || strtolower($nama) === 'nama peserta') {
                $skipCount++;
                continue;
            }

            // Normalisasi nama (hapus karakter aneh, trim extra spaces)
            $cleanName = preg_replace('/[^\x20-\x7E\x{00C0}-\x{024F}]/u', '', $nama);
            if ($cleanName !== null) {
                $nama = $cleanName;
            } else {
                // Fallback: hapus karakter non-ascii standar jika /u gagal karena invalid utf8
                $nama = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $nama);
            }
            // Hapus karakter ? jika ada di akhir nama (biasanya hasil salah encoding)
            $nama = rtrim($nama, '?');
            $nama = preg_replace('/\s+/', ' ', trim($nama));

            // Resolve tim kerja dari penempatan
            $idTimKerja1 = $this->resolveTimKerjaId($penempatan, $map, $timKerjaCache);

            if (!$idTimKerja1) {
                // Fallback: tim pertama yang tersedia
                $idTimKerja1 = TimKerja::first()->id ?? 1;
                $failCount++;
            }

            // Pilihan 2: random tim yang berbeda dari pilihan 1
            $idTimKerja2 = TimKerja::where('id', '!=', $idTimKerja1)
                ->inRandomOrder()
                ->first()->id ?? $idTimKerja1;

            // Normalisasi nomor telepon
            $phone = $this->normalizePhone($nomorTelp);

            // Generate email dari nama
            $emailName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '.', $nama));
            $emailName = preg_replace('/\.+/', '.', trim($emailName, '.'));

            // Generate email institusi
            $institusiClean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $namaInstitusi));

            PesertaMagang::create([
                'nama' => $nama,
                'nim_nis' => $nim_nis,
                'tingkat_pendidikan' => $tingkatPendidikan,
                'nama_institusi' => $namaInstitusi,
                'jurusan' => $jurusan,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'nomor_telp' => $phone,
                'email' => $emailRaw ?: $emailName . '@gmail.com',
                'email_institusi' => $emailInstitusiRaw ?: 'kampus@' . $institusiClean . '.ac.id',
                'id_tim_kerja_1' => $idTimKerja1,
                'id_tim_kerja_2' => $idTimKerja2,
                'cv' => null,
                'surat_rekomendasi' => 'surat_dummy.pdf',
                'status_magang' => $statusMagang,
                'is_sk_sent' => $statusMagang === 'Selesai' ? 1 : 0,
                'is_evaluasi_sent' => $statusMagang === 'Selesai' ? 1 : 0,
                'is_sertifikat_sent' => $statusMagang === 'Selesai' ? 1 : 0,
            ]);

            $successCount++;
        }

        fclose($file);

        $this->command->info("✅ Selesai! Berhasil: {$successCount}, Dilewati: {$skipCount}, Gagal mapping: {$failCount}");
    }
}
