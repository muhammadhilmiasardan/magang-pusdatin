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

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminDashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/lamaran', [\App\Http\Controllers\LamaranMasukController::class, 'index'])->name('lamaran.index');
    Route::post('/lamaran/{id}/terima', [\App\Http\Controllers\LamaranMasukController::class, 'terima'])->name('lamaran.terima');
    Route::post('/lamaran/{id}/tolak', [\App\Http\Controllers\LamaranMasukController::class, 'tolak'])->name('lamaran.tolak');
    Route::get('/manajemen', [\App\Http\Controllers\ManajemenMagangController::class, 'index'])->name('manajemen.index');
    Route::get('/manajemen/{id}', [\App\Http\Controllers\ManajemenMagangController::class, 'show'])->name('manajemen.show');
    Route::get('/dokumen', [\App\Http\Controllers\PusatDokumenController::class, 'index'])->name('dokumen.index');
});