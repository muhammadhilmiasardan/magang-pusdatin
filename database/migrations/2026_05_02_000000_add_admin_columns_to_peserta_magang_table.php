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
            // Kita ubah default status_magang (Laravel's change() method requires doctrine/dbal, 
            // but for simplicity we just make sure the new logic handles it, or just alter it)
            // It's safer to drop and recreate the column if it's just a status string, or just add the new ones.
            // Since we'll recreate the DB with fresh seed anyway, we can just update the previous migration 
            // or just add the new columns here and change default in the original migration or here.
            
            // To avoid doctrine/dbal requirement for change(), we'll just add the new columns here
            $table->boolean('is_sk_sent')->default(false);
            $table->boolean('is_evaluasi_sent')->default(false);
            $table->boolean('is_sertifikat_sent')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peserta_magang', function (Blueprint $table) {
            $table->dropColumn(['is_sk_sent', 'is_evaluasi_sent', 'is_sertifikat_sent']);
        });
    }
};
