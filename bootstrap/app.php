<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware untuk auto-aktivasi peserta berdasarkan tanggal mulai
        // Berjalan setiap kali halaman admin diakses — pengganti cron job
        $middleware->alias([
            'aktifkan.peserta' => \App\Http\Middleware\AktifkanPesertaMagang::class,
        ]);

        // Jika user sudah login lalu mencoba akses halaman guest (e.g. /login),
        // redirect ke dashboard admin, bukan ke landing page.
        $middleware->redirectUsersTo('/admin/dashboard');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
