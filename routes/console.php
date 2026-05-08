<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jalankan setiap hari jam 00:05 untuk mengaktifkan peserta yang tanggal mulai-nya sudah tiba
Schedule::command('magang:aktifkan-peserta')->dailyAt('00:05');
