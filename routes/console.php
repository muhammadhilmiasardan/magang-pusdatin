<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jalankan setiap hari jam 00:05 untuk mengaktifkan peserta yang tanggal mulai-nya sudah tiba
Schedule::command('magang:aktifkan-peserta')->dailyAt('00:05');

// Jalankan setiap hari jam 01:00 pagi untuk membersihkan file lampiran yang sudah kedaluwarsa
Schedule::command('magang:cleanup-berkas')->dailyAt('01:00');
