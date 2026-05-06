<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peserta_magang', function (Blueprint $table) {
            // Path file surat penerimaan final yang sudah di-TTE dan diupload admin
            $table->string('surat_penerimaan_final')->nullable()->after('surat_rekomendasi');
        });
    }

    public function down(): void
    {
        Schema::table('peserta_magang', function (Blueprint $table) {
            $table->dropColumn('surat_penerimaan_final');
        });
    }
};
