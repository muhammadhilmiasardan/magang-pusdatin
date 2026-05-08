<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PesertaMagang;
use Carbon\Carbon;

class AktifkanPesertaMagang extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magang:aktifkan-peserta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis mengubah status peserta dari "Belum Aktif" menjadi "Aktif" apabila tanggal mulai magang sudah tiba.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        // Cari peserta berstatus 'Belum Aktif' yang tanggal_mulai-nya <= hari ini
        $peserta = PesertaMagang::where('status_magang', 'Belum Aktif')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->get();

        if ($peserta->isEmpty()) {
            $this->info('[' . $today->toDateString() . '] Tidak ada peserta yang perlu diaktifkan.');
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($peserta as $p) {
            $p->status_magang = 'Aktif';
            $p->save();
            $count++;
            $this->line("  ✓ Diaktifkan: {$p->nama} (mulai: {$p->tanggal_mulai})");
        }

        $this->info("[{$today->toDateString()}] {$count} peserta berhasil diubah statusnya menjadi 'Aktif'.");

        return Command::SUCCESS;
    }
}
