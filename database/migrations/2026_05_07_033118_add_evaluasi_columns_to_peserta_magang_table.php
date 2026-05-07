<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('peserta_magang', function (Blueprint $table) {
            $table->json('evaluasi_data')->nullable()->after('surat_keterangan');
            $table->string('surat_evaluasi')->nullable()->after('evaluasi_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peserta_magang', function (Blueprint $table) {
            $table->dropColumn(['evaluasi_data', 'surat_evaluasi']);
        });
    }
};
