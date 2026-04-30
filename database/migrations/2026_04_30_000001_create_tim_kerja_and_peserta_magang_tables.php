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
        Schema::create('tim_kerja', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tim');
            $table->string('bidang');
            $table->integer('kuota_maksimal')->default(5);
            $table->timestamps();
        });

        Schema::create('peserta_magang', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('tingkat_pendidikan'); // Universitas / SMK
            $table->string('nama_institusi');
            $table->string('jurusan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('nomor_telp');
            $table->string('email');
            $table->string('email_institusi');
            $table->foreignId('id_tim_kerja_1')->constrained('tim_kerja')->onDelete('cascade');
            $table->foreignId('id_tim_kerja_2')->constrained('tim_kerja')->onDelete('cascade');
            $table->string('cv')->nullable(); // path file, opsional
            $table->string('surat_rekomendasi'); // path file, wajib
            $table->string('status_magang')->default('Aktif'); // Aktif / Selesai
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta_magang');
        Schema::dropIfExists('tim_kerja');
    }
};
