<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PesertaMagang;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupBerkas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magang:cleanup-berkas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membersihkan file unggahan (CV, Surat Permohonan, Pas Foto) untuk peserta yang ditolak lebih dari 7 hari untuk menghemat ruang disk.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai proses pembersihan berkas...');
        $countDeleted = 0;
        $totalBytesFreed = 0;

        // 1. Peserta yang DITOLAK lebih dari 7 hari yang lalu
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $pesertaDitolak = PesertaMagang::where('status_magang', 'Ditolak')
            ->where('updated_at', '<', $sevenDaysAgo)
            ->get();

        /** @var \App\Models\PesertaMagang $peserta */
        foreach ($pesertaDitolak as $peserta) {
            $bytes = $this->deleteFilesForPeserta($peserta);
            if ($bytes > 0) {
                $countDeleted++;
                $totalBytesFreed += $bytes;
            }
        }

        $mbFreed = round($totalBytesFreed / 1048576, 2);
        
        if ($countDeleted > 0) {
            $this->info("Berhasil membersihkan berkas dari {$countDeleted} peserta. Ruang disk yang dihemat: {$mbFreed} MB.");
        } else {
            $this->info('Tidak ada berkas yang perlu dibersihkan saat ini.');
        }

        return self::SUCCESS;
    }

    /**
     * Helper untuk menghapus file fisik tanpa menghapus record database.
     * Mengembalikan total byte yang berhasil dihapus.
     */
    private function deleteFilesForPeserta(PesertaMagang $peserta)
    {
        $bytesFreed = 0;
        $filesToCheck = ['cv', 'surat_rekomendasi', 'pas_foto'];

        foreach ($filesToCheck as $field) {
            if (!empty($peserta->{$field}) && Storage::disk('public')->exists($peserta->{$field})) {
                $bytesFreed += Storage::disk('public')->size($peserta->{$field});
                Storage::disk('public')->delete($peserta->{$field});
                
                // Kosongkan path di database agar tidak terjadi error path not found
                $peserta->{$field} = null;
            }
        }

        if ($bytesFreed > 0) {
            $peserta->save();
            $this->line("  ✓ Menghapus lampiran pendaftar: {$peserta->nama}");
        }

        return $bytesFreed;
    }
}
