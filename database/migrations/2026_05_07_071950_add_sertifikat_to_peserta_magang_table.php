<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peserta_magang', function (Blueprint $table) {
            // JSON menyimpan draft data sertifikat (nomor_sertifikat, predikat)
            $table->json('sertifikat_data')->nullable()->after('surat_evaluasi');
            // Path file sertifikat final yang sudah di-TTE dan diupload admin
            $table->string('surat_sertifikat')->nullable()->after('sertifikat_data');
        });
    }

    public function down(): void
    {
        Schema::table('peserta_magang', function (Blueprint $table) {
            $table->dropColumn(['sertifikat_data', 'surat_sertifikat']);
        });
    }
};
