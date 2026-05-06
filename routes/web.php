<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PendaftaranController;

// Redirect root URL ke halaman pendaftaran
Route::get('/', function () {
    return redirect('/pendaftaran');
});

// Route untuk menampilkan form pendaftaran
Route::get('/pendaftaran', [PendaftaranController::class, 'create'])->name('pendaftaran.create');

// Route untuk menangani submit form (Method POST)
Route::post('/pendaftaran', [PendaftaranController::class, 'store'])->name('pendaftaran.store');

// Route halaman sukses setelah submit
Route::get('/pendaftaran/sukses', function () {
    return view('pendaftaran.sukses');
})->name('pendaftaran.sukses');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminDashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/lamaran', [\App\Http\Controllers\LamaranMasukController::class, 'index'])->name('lamaran.index');
    Route::get('/lamaran/{id}', [\App\Http\Controllers\LamaranMasukController::class, 'show'])->name('lamaran.show');
    Route::post('/lamaran/{id}/tolak', [\App\Http\Controllers\LamaranMasukController::class, 'tolak'])->name('lamaran.tolak');
    Route::post('/lamaran/{id}/upload-surat', [\App\Http\Controllers\LamaranMasukController::class, 'uploadSurat'])->name('lamaran.upload-surat');
    Route::post('/lamaran/{id}/kirim-email', [\App\Http\Controllers\LamaranMasukController::class, 'kirimEmail'])->name('lamaran.kirim-email');
    Route::post('/lamaran/{id}/surat/preview', [\App\Http\Controllers\SuratPenerimaanController::class, 'preview'])->name('lamaran.surat.preview');
    Route::post('/lamaran/{id}/surat/download', [\App\Http\Controllers\SuratPenerimaanController::class, 'download'])->name('lamaran.surat.download');
    Route::get('/manajemen', [\App\Http\Controllers\ManajemenMagangController::class, 'index'])->name('manajemen.index');
    Route::get('/manajemen/{id}', [\App\Http\Controllers\ManajemenMagangController::class, 'show'])->name('manajemen.show');
    Route::post('/manajemen/{id}/anulir', [\App\Http\Controllers\ManajemenMagangController::class, 'anulir'])->name('manajemen.anulir');
    Route::get('/dokumen', [\App\Http\Controllers\PusatDokumenController::class, 'index'])->name('dokumen.index');
    Route::get('/foto-akses', [\App\Http\Controllers\FotoAksesController::class, 'index'])->name('foto-akses.index');
});