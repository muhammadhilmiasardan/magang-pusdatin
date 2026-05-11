<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PusatDokumenController;

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

// Auth Routes
Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'aktifkan.peserta'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminDashboardController::class, 'index'])->name('dashboard.index');
    
    // Profile Routes
    Route::get('/profil', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profil', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
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
    
    Route::prefix('dokumen')->name('dokumen.')->group(function () {
        Route::get('/', [PusatDokumenController::class, 'index'])->name('index');
        Route::get('/sk-magang/{id}/preview', [PusatDokumenController::class, 'previewSkMagang'])->name('sk-magang.preview');
        Route::get('/sk-magang/{id}/download', [PusatDokumenController::class, 'downloadSkMagang'])->name('sk-magang.download');
        Route::post('/sk-magang/{id}/upload-kirim', [PusatDokumenController::class, 'uploadDanKirimSkMagang'])->name('sk-magang.upload-kirim');

        Route::post('/evaluasi/{id}/simpan-draft', [PusatDokumenController::class, 'saveDraftEvaluasi'])->name('evaluasi.simpan-draft');
        Route::get('/evaluasi/{id}/preview', [PusatDokumenController::class, 'previewEvaluasi'])->name('evaluasi.preview');
        Route::get('/evaluasi/{id}/download', [PusatDokumenController::class, 'downloadEvaluasi'])->name('evaluasi.download');
        Route::post('/evaluasi/{id}/upload-kirim', [PusatDokumenController::class, 'uploadDanKirimEvaluasi'])->name('evaluasi.upload-kirim');

        Route::post('/sertifikat/{id}/simpan-draft', [PusatDokumenController::class, 'saveDraftSertifikat'])->name('sertifikat.simpan-draft');
        Route::get('/sertifikat/{id}/preview', [PusatDokumenController::class, 'previewSertifikat'])->name('sertifikat.preview');
        Route::get('/sertifikat/{id}/download', [PusatDokumenController::class, 'downloadSertifikat'])->name('sertifikat.download');
        Route::post('/sertifikat/{id}/upload-kirim', [PusatDokumenController::class, 'uploadDanKirimSertifikat'])->name('sertifikat.upload-kirim');
    });
    
    Route::prefix('arsip-dokumen')->name('arsip-dokumen.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ArsipDokumenController::class, 'index'])->name('index');
        Route::post('/download-bulk', [\App\Http\Controllers\ArsipDokumenController::class, 'downloadBulk'])->name('download-bulk');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminUserController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\AdminUserController::class, 'store'])->name('store');
        Route::put('/{id}', [\App\Http\Controllers\AdminUserController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\AdminUserController::class, 'destroy'])->name('destroy');
    });

    Route::get('/foto-akses', [\App\Http\Controllers\FotoAksesController::class, 'index'])->name('foto-akses.index');
});