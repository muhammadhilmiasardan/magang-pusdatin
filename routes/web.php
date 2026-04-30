<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PendaftaranController; // Import controllernya di atas

// Redirect root URL ke halaman pendaftaran
Route::get('/', function () {
    return redirect('/pendaftaran');
});

// Route untuk menampilkan form pendaftaran (Saat user buka /pendaftaran)
Route::get('/pendaftaran', [PendaftaranController::class, 'create'])->name('pendaftaran.create');

// Route untuk menangani submit form (Method POST)
Route::post('/pendaftaran', [PendaftaranController::class, 'store'])->name('pendaftaran.store');