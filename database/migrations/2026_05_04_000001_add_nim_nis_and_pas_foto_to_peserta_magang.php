<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peserta_magang', function (Blueprint $table) {
            $table->string('nim_nis')->nullable()->after('jurusan');
            $table->string('pas_foto')->nullable()->after('surat_rekomendasi');
        });
    }

    public function down(): void
    {
        Schema::table('peserta_magang', function (Blueprint $table) {
            $table->dropColumn(['nim_nis', 'pas_foto']);
        });
    }
};
