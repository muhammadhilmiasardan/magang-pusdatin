<?php

namespace App\Http\Controllers;

use App\Models\TimKerja;
use App\Models\PesertaMagang;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    /**
     * Generate 4 triwulan ke depan (sama dengan PendaftaranController)
     */
    private function generateTriwulans(): array
    {
        $currentMonth = (int) date('n');
        $currentYear  = (int) date('Y');
        $currentQuarter = (int) ceil($currentMonth / 3);
        $triwulans = [];

        for ($i = 0; $i < 4; $i++) {
            $q = $currentQuarter + $i;
            $y = $currentYear;
            if ($q > 4) { $q -= 4; $y++; }

            $sm = str_pad(($q - 1) * 3 + 1, 2, '0', STR_PAD_LEFT);
            $em = str_pad($q * 3, 2, '0', STR_PAD_LEFT);
            $ed = ($q == 2 || $q == 3) ? '30' : '31';

            $labels = [1=>'(Januari – Maret)',2=>'(April – Juni)',3=>'(Juli – September)',4=>'(Oktober – Desember)'];

            $triwulans[] = [
                'value' => "Triwulan $q $y",
                'label' => "Triwulan $q",
                'year'  => $y,
                'bulan' => $labels[$q],
                'q'     => $q,
                'index' => $i,
                'start_date' => "$y-$sm-01",
                'end_date'   => "$y-$em-$ed",
            ];
        }
        return $triwulans;
    }

    /**
     * Tampilkan halaman landing portal magang
     */
    public function index()
    {
        // Get all teams dengan kuota count
        $timKerjaBidang = TimKerja::withAktifCount()
            ->orderBy('bidang')
            ->orderBy('nama_tim')
            ->get()
            ->groupBy('bidang');

        // Get active peserta untuk quota calculation client-side (sama dengan form pendaftaran)
        $activePeserta = PesertaMagang::whereIn('status_magang', ['Belum Aktif', 'Aktif'])
            ->select('id', 'id_tim_kerja_1', 'tanggal_mulai', 'tanggal_selesai')
            ->get();

        // Stats untuk hero section
        $pesertaAktifCount = PesertaMagang::where('status_magang', 'Aktif')->count();
        $totalAlumni       = PesertaMagang::where('status_magang', 'Selesai')->count();

        // Get all teams list (untuk JS)
        $allTeams = TimKerja::all();

        // Triwulan data (sama dengan PendaftaranController)
        $triwulans = $this->generateTriwulans();

        // Map bidang untuk UI display
        $bidangInfo = [
            'Bidang Manajemen Teknologi Informasi' => [
                'icon' => 'fa-microchip',
                'color' => '#3b82f6',
                'description' => 'Pengelolaan infrastruktur dan keamanan teknologi informasi Kementerian PU'
            ],
            'Bidang Data Analitik Pekerjaan Umum' => [
                'icon' => 'fa-chart-bar',
                'color' => '#8b5cf6',
                'description' => 'Analisis data dan informasi geografis untuk infrastruktur pekerjaan umum'
            ],
            'Bidang Pengelolaan Data Bencana Infrastruktur' => [
                'icon' => 'fa-exclamation-triangle',
                'color' => '#ec4899',
                'description' => 'Manajemen data dan informasi kebencanaan infrastruktur'
            ],
            'Subbagian Tata Usaha' => [
                'icon' => 'fa-briefcase',
                'color' => '#f59e0b',
                'description' => 'Administrasi, keuangan, kepegawaian, dan pengelolaan operasional'
            ],
        ];

        return view('welcome', compact('timKerjaBidang', 'activePeserta', 'allTeams', 'bidangInfo', 'triwulans', 'pesertaAktifCount', 'totalAlumni'));
    }

    /**
     * API endpoint untuk real-time quota calculation
     */
    public function getQuotaStatus(Request $request)
    {
        $timId = $request->query('tim_id');
        $tanggalMulai = $request->query('tanggal_mulai');
        $tanggalSelesai = $request->query('tanggal_selesai');

        $tim = TimKerja::withAktifCount()->find($timId);

        if (!$tim) {
            return response()->json(['error' => 'Tim tidak ditemukan'], 404);
        }

        // Count overlapping interns
        $overlapCount = 0;
        if ($tanggalMulai && $tanggalSelesai) {
            $overlapCount = PesertaMagang::where('id_tim_kerja_1', $timId)
                ->whereIn('status_magang', ['Belum Aktif', 'Aktif'])
                ->where('tanggal_mulai', '<=', $tanggalSelesai)
                ->where('tanggal_selesai', '>=', $tanggalMulai)
                ->count();
        } else {
            // Use total count if dates not provided
            $overlapCount = $tim->peserta_magang_count;
        }

        $sisaKuota = max(0, $tim->kuota_maksimal - $overlapCount);
        $persentase = $tim->kuota_maksimal > 0 ? round(($overlapCount / $tim->kuota_maksimal) * 100) : 0;

        return response()->json([
            'tim_id' => $timId,
            'nama_tim' => $tim->nama_tim,
            'kuota_maksimal' => $tim->kuota_maksimal,
            'terisi' => $overlapCount,
            'sisa' => $sisaKuota,
            'persentase' => $persentase,
            'status' => $sisaKuota > 0 ? 'available' : 'full',
        ]);
    }
}
